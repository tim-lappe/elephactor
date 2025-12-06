<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;

final readonly class AttributeNode extends AbstractNode
{
    /**
     * @param list<AttributeArgumentNode> $arguments
     */
    public function __construct(
        QualifiedNameNode $name,
        array $arguments = []
    ) {
        parent::__construct();

        $this->children()->add("name", $name);

        foreach ($arguments as $argument) {
            $this->children()->add("argument", $argument);
        }
    }

    public function name(): QualifiedNameNode
    {
        return $this->children()->getOne("name", QualifiedNameNode::class) ?? throw new \RuntimeException('Qualified name not found');
    }

    /**
     * @return list<AttributeArgumentNode>
     */
    public function arguments(): array
    {
        return $this->children()->getAllOf("argument", AttributeArgumentNode::class);
    }
}
