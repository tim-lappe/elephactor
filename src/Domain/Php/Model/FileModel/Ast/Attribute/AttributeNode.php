<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class AttributeNode extends AbstractNode
{
    /**
     * @param list<AttributeArgumentNode> $arguments
     */
    public function __construct(
        private readonly QualifiedName $name,
        private readonly array $arguments = []
    ) {
        parent::__construct(NodeKind::ATTRIBUTE);
    }

    public function name(): QualifiedName
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
