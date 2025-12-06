<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class UseStatementNode extends AbstractNode implements StatementNode
{
    private ?QualifiedNameNode $groupPrefix;
    /**
     * @param list<UseClauseNode> $clauses
     */
    public function __construct(
        private readonly array $clauses,
        private readonly UseKind $kind = UseKind::CLASS_IMPORT,
        ?QualifiedName $groupPrefix = null,
    ) {
        if ($clauses === []) {
            throw new \InvalidArgumentException('Use statement must contain at least one clause');
        }

        parent::__construct();

        $this->groupPrefix = $groupPrefix !== null ? new QualifiedNameNode($groupPrefix, $this) : null;
    }

    public function useKind(): UseKind
    {
        return $this->kind;
    }

    public function importsClassIdentifier(Identifier $classIdentifier): bool
    {
        foreach ($this->clauses as $clause) {
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
     * @return list<UseClauseNode>
     */
    public function clauses(): array
    {
        return $this->clauses;
    }

    public function groupPrefix(): ?QualifiedNameNode
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
        $children = $this->clauses;

        if ($this->groupPrefix !== null) {
            $children[] = $this->groupPrefix;
        }

        return $children;
    }
}
