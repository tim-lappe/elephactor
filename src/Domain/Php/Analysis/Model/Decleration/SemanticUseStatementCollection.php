<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticIdentifierNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseClauseNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNodeCollection;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedNameCollection;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class SemanticUseStatementCollection extends AbstractSemanticNode
{
    /**
     * @var list<SemanticUseStatement> $importItems
     */
    private array $importItems = [];

    public function __construct(
        private readonly NamespacedScope $namespaceScope,
        private readonly UseStatementNodeCollection $useStatementNodeCollection,
    ) {
        $this->reload();
    }

    public function reload(): void
    {
        $importLists = [];
        $useStatements = $this->useStatementNodeCollection->toArray();
    
        foreach ($useStatements as $useStatement) {
            if ($useStatement->useKind() !== UseKind::CLASS_IMPORT) {
                continue;
            }

            foreach ($useStatement->clauses() as $clause) {
                $importLists[] = new SemanticUseStatement(
                    new SemanticQualifiedNameNode($this->namespaceScope, $clause->name(), NameKind::UseStatement),
                    $clause->alias() !== null ? new SemanticIdentifierNode($this->namespaceScope, $clause->alias()) : null,
                    $useStatement->groupPrefix() !== null ? new SemanticQualifiedNameNode($this->namespaceScope, $useStatement->groupPrefix(), NameKind::UseStatement) : null
                );
            }
        }

        $this->importItems = $importLists;
    }

    public function children(): array
    {
        return [...parent::children(), ...$this->importItems];
    }

    public function __toString(): string
    {
        return 'ImportList: ' . implode(', ', array_map(fn (SemanticUseStatement $importItem) => $importItem->__toString(), $this->importItems));
    }

    /**
     * @return FullyQualifiedNameCollection
     */
    public function fullyQualifiedNames(): FullyQualifiedNameCollection
    {
        $fullyQualifiedNames = new FullyQualifiedNameCollection();
        foreach ($this->importItems as $importItem) {
            $fullyQualifiedNames->add($importItem->fullyQualifiedName());
        }

        return $fullyQualifiedNames;
    }

    public function addNewImport(FullyQualifiedName $fullyQualifiedName): void
    {
        $this->useStatementNodeCollection->add(new UseStatementNode(
            [new UseClauseNode($fullyQualifiedName, null)],
            UseKind::CLASS_IMPORT,
            null,
        ));
    }

    public function getByAlias(Identifier $alias): ?SemanticUseStatement
    {
        foreach ($this->importItems as $importItem) {
            if ($importItem->alias()?->identifier()->equals($alias) === true) {
                return $importItem;
            }
        }

        return null;
    }

    public function getByFullyQualifiedName(FullyQualifiedName $fullyQualifiedName): ?SemanticUseStatement
    {
        foreach ($this->importItems as $importItem) {
            if ($importItem->fullyQualifiedName()->equals($fullyQualifiedName)) {
                return $importItem;
            }
        }
        return null;
    }

    public function getByQualifiedName(QualifiedName $qualifiedName): ?SemanticUseStatement
    {
        foreach ($this->importItems as $importItem) {
            if ($importItem->fullyQualifiedName()->endsWith($qualifiedName)) {
                return $importItem;
            }
        }

        return null;
    }

    public function resolve(QualifiedName $qualifiedName): ?FullyQualifiedName
    {
        $parts = $qualifiedName->parts();
        if ($parts === []) {
            return null;
        }

        $firstPart = $parts[0];

        foreach ($this->importItems as $importItem) {
            $alias = $importItem->alias();
            if ($alias !== null && $alias->identifier()->equals($firstPart)) {
                if (count($parts) === 1) {
                    return $importItem->fullyQualifiedName();
                }

                $remainingParts = array_slice($parts, 1);

                return new FullyQualifiedName([...$importItem->fullyQualifiedName()->parts(), ...$remainingParts]);
            }

            if ($alias === null && count($parts) === 1 && $importItem->fullyQualifiedName()->lastPart()->equals($firstPart)) {
                return $importItem->fullyQualifiedName();
            }
        }

        return null;
    }
}
