<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;

final class AttributeGroupNode extends AbstractNode
{
    /**
     * @param list<AttributeNode> $attributes
     */
    public function __construct(
        array $attributes
    ) {
        if ($attributes === []) {
            throw new \InvalidArgumentException('Attribute group cannot be empty');
        }

        parent::__construct();

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }
    }

    /**
     * @return list<AttributeNode>
     */
    public function attributes(): array
    {
        return $this->children()->getAllOf("attribute", AttributeNode::class);
    }
}
