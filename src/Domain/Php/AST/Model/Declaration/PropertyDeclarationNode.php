<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
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
        private readonly array $properties,
        private readonly array $attributes = [],
        private readonly ?TypeNode $type = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        if ($properties === []) {
            throw new \InvalidArgumentException('Property declaration requires at least one property');
        }

        parent::__construct(NodeKind::PROPERTY_DECLARATION);
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
        return $this->properties;
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function type(): ?TypeNode
    {
        return $this->type;
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            ...$this->attributes,
            ...$this->properties,
        ];

        if ($this->type !== null) {
            $children[] = $this->type;
        }

        return $children;
    }
}
