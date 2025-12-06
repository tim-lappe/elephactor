<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Visibility;

final readonly class ClassConstantDeclarationNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<ConstElementNode>   $elements
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        private readonly Visibility $visibility,
        array $elements,
        array $attributes = [],
        private readonly bool $final = false,
        ?TypeNode $type = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        if ($elements === []) {
            throw new \InvalidArgumentException('Class constant declaration requires elements');
        }

        parent::__construct();

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }

        foreach ($elements as $element) {
            $this->children()->add("element", $element);
        }

        if ($type !== null) {
            $this->children()->add("type", $type);
        }
    }

    public function visibility(): Visibility
    {
        return $this->visibility;
    }

    /**
     * @return list<ConstElementNode>
     */
    public function elements(): array
    {
        return $this->children()->getAllOf("element", ConstElementNode::class);
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->children()->getAllOf("attribute", AttributeGroupNode::class);
    }

    public function isFinal(): bool
    {
        return $this->final;
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
