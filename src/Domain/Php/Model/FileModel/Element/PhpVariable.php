<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Element;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\ParameterPassingMode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Visibility;

final class PhpVariable
{
    public function __construct(
        private readonly ParameterNode $node,
    ) {
    }

    public function name(): string
    {
        return $this->node->name()->value();
    }

    public function type(): ?TypeNode
    {
        return $this->node->type();
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->node->defaultValue();
    }

    public function passingMode(): ParameterPassingMode
    {
        return $this->node->passingMode();
    }

    public function isVariadic(): bool
    {
        return $this->node->isVariadic();
    }

    public function promotedVisibility(): ?Visibility
    {
        return $this->node->promotedVisibility();
    }

    public function isPromotedReadonly(): bool
    {
        return $this->node->isPromotedReadonly();
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->node->attributes();
    }

    public function node(): ParameterNode
    {
        return $this->node;
    }
}
