<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

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
