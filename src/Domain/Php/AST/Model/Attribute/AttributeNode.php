<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class AttributeNode extends AbstractNode
{
    private QualifiedNameNode $name;

    /**
     * @param list<AttributeArgumentNode> $arguments
     */
    public function __construct(
        QualifiedName $name,
        private readonly array $arguments = []
    ) {
        parent::__construct(NodeKind::ATTRIBUTE);
        $this->name = new QualifiedNameNode($name, $this);
    }

    public function name(): QualifiedNameNode
    {
        return $this->name;
    }

    /**
     * @return list<AttributeArgumentNode>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->arguments;
    }
}
