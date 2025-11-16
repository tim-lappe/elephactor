<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class NamespaceDefinitionNode extends AbstractNode implements StatementNode
{
    private QualifiedNameNode $name;
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        QualifiedName $name,
        private readonly array $statements,
        private readonly bool $bracketed = false
    ) {
        parent::__construct(NodeKind::NAMESPACE_DEFINITION);

        $this->name = new QualifiedNameNode($name, $this);
    }

    public function name(): QualifiedNameNode
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
        $children = $this->statements;

        $children[] = $this->name;

        return $children;
    }
}
