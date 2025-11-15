<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class NamespaceDefinitionNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly ?QualifiedName $name,
        private readonly array $statements,
        private readonly bool $bracketed = false
    ) {
        parent::__construct(NodeKind::NAMESPACE_DEFINITION);
    }

    public function name(): ?QualifiedName
    {
        return $this->name;
    }

    public function isBracketed(): bool
    {
        return $this->bracketed;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->statements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->statements;
    }
}
