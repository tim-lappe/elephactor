<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class AttributeGroupNode extends AbstractNode
{
    /**
     * @param list<AttributeNode> $attributes
     */
    public function __construct(
        private readonly array $attributes
    ) {
        if ($attributes === []) {
            throw new \InvalidArgumentException('Attribute group cannot be empty');
        }

        parent::__construct(NodeKind::ATTRIBUTE_GROUP);
    }

    /**
     * @return list<AttributeNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->attributes;
    }
}
