<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\PropertyModifiers;

final class PropertyDeclarationNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<PropertyNode>       $properties
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        private readonly PropertyModifiers $modifiers,
        array $properties,
        array $attributes,
        ?TypeNode $type = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        if ($properties === []) {
            throw new \InvalidArgumentException('Property declaration requires at least one property');
        }

        parent::__construct();

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }

        foreach ($properties as $property) {
            $this->children()->add("property", $property);
        }

        if ($type !== null) {
            $this->children()->add("type", $type);
        }
    }

    public function modifiers(): PropertyModifiers
    {
        return $this->modifiers;
    }

    /**
     * @return list<PropertyNode>
     */
    public function properties(): array
    {
        return $this->children()->getAllOf("property", PropertyNode::class);
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->children()->getAllOf("attribute", AttributeGroupNode::class);
    }

    public function type(): ?TypeNode
    {
        return $this->children()->getOne("type", TypeNode::class);
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }
}
