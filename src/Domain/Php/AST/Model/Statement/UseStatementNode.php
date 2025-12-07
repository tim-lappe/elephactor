<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class UseStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<UseClauseNode> $clauses
     */
    public function __construct(
        array $clauses,
        private readonly UseKind $kind = UseKind::CLASS_IMPORT,
        ?QualifiedName $groupPrefix = null,
    ) {
        if ($clauses === []) {
            throw new \InvalidArgumentException('Use statement must contain at least one clause');
        }

        parent::__construct();

        $groupPrefixNode = $groupPrefix !== null ? new QualifiedNameNode($groupPrefix) : null;

        foreach ($clauses as $clause) {
            $this->children()->add('clause', $clause);
        }

        if ($groupPrefixNode !== null) {
            $this->children()->add('groupPrefix', $groupPrefixNode);
        }
    }

    public function useKind(): UseKind
    {
        return $this->kind;
    }

    public function importsClassIdentifier(Identifier $classIdentifier): bool
    {
        foreach ($this->children()->getAllOf('clause', UseClauseNode::class) as $clause) {
            $qualifiedName = $clause->name()->qualifiedName();
            if ($classIdentifier->equals($qualifiedName->lastPart())) {
                return true;
            }

            $alias = $clause->alias();
            if ($alias !== null && $classIdentifier->equals($alias->identifier())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<Identifier>
     */
    public function identifiersImported(): array
    {
        $clauses = $this->clauses();
        $identifiers = [];

        foreach ($clauses as $clause) {
            $alias = $clause->alias();
            if ($alias !== null) {
                $identifiers[] = $alias->identifier();
                continue;
            }

            $identifiers[] = $clause->name()->qualifiedName()->lastPart();
        }

        return $identifiers;
    }

    /**
     * @return list<UseClauseNode>
     */
    public function clauses(): array
    {
        return $this->children()->getAllOf('clause', UseClauseNode::class);
    }

    public function groupPrefix(): ?QualifiedNameNode
    {
        return $this->children()->getOne('groupPrefix', QualifiedNameNode::class);
    }

    public function isGroupImport(): bool
    {
        return $this->children()->getOne('groupPrefix', QualifiedNameNode::class) !== null;
    }

    public function removeGroupPrefix(): void
    {
        $this->children()->remove('groupPrefix');
    }
}
