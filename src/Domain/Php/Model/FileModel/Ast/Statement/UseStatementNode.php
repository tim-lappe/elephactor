<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class UseStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<UseClauseNode> $clauses
     */
    public function __construct(
        private readonly array $clauses,
        private readonly UseKind $kind = UseKind::CLASS_IMPORT,
        private readonly ?QualifiedName $groupPrefix = null,
    ) {
        if ($clauses === []) {
            throw new \InvalidArgumentException('Use statement must contain at least one clause');
        }

        parent::__construct(NodeKind::USE_STATEMENT);
    }

    public function useKind(): UseKind
    {
        return $this->kind;
    }

    public function importsClassIdentifier(Identifier $classIdentifier): bool
    {
        foreach ($this->clauses as $clause) {
            $qualifiedName = $clause->name();
            if ($classIdentifier->equals($qualifiedName->lastPart())) {
                return true;
            }

            $alias = $clause->alias();
            if ($alias !== null && $classIdentifier->equals($alias)) {
                return true;
            }
        }

        return false;
    }

    public function renameClassIdentifier(Identifier $classIdentifier, Identifier $newClassIdentifier): void
    {
        if (!$this->importsClassIdentifier($classIdentifier)) {
            throw new \InvalidArgumentException('Class identifier not found in use statement');
        }

        foreach ($this->clauses as $clause) {
            $qualifiedName = $clause->name();
            if ($classIdentifier->equals($qualifiedName->lastPart())) {
                $qualifiedName->changeLastPart($newClassIdentifier);
            }
        }
    }

    /**
     * @return list<UseClauseNode>
     */
    public function clauses(): array
    {
        return $this->clauses;
    }

    public function groupPrefix(): ?QualifiedName
    {
        return $this->groupPrefix;
    }

    public function isGroupImport(): bool
    {
        return $this->groupPrefix !== null;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->clauses;
    }
}
