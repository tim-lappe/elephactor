<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Element;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\PropertyDeclarationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\PropertyNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\PropertyModifiers;

final class PhpProperty
{
    public function __construct(
        private readonly PropertyDeclarationNode $declaration,
        private readonly PropertyNode $node,
    ) {
    }

    public function name(): string
    {
        return $this->node->name()->value();
    }

    public function modifiers(): PropertyModifiers
    {
        return $this->declaration->modifiers();
    }

    public function type(): ?TypeNode
    {
        return $this->declaration->type();
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->node->defaultValue();
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->declaration->attributes();
    }

    public function docBlock(): ?DocBlock
    {
        return $this->declaration->docBlock();
    }

    public function declaration(): PropertyDeclarationNode
    {
        return $this->declaration;
    }

    public function node(): PropertyNode
    {
        return $this->node;
    }
}
