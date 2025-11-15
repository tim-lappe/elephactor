<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Visibility;

final class ClassConstantDeclarationNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<ConstElementNode>   $elements
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        private readonly Visibility $visibility,
        private readonly array $elements,
        private readonly array $attributes = [],
        private readonly bool $final = false,
        private readonly ?TypeNode $type = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        if ($elements === []) {
            throw new \InvalidArgumentException('Class constant declaration requires elements');
        }

        parent::__construct(NodeKind::CLASS_CONSTANT_DECLARATION);
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
        return $this->elements;
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function isFinal(): bool
    {
        return $this->final;
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
            ...$this->elements,
        ];

        if ($this->type !== null) {
            $children[] = $this->type;
        }

        return $children;
    }
}
