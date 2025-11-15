<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\ParameterPassingMode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Visibility;

final class ParameterNode extends AbstractNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        private readonly Identifier $name,
        private readonly ?TypeNode $type = null,
        private readonly ParameterPassingMode $passingMode = ParameterPassingMode::BY_VALUE,
        private readonly bool $variadic = false,
        private readonly ?ExpressionNode $defaultValue = null,
        private readonly ?Visibility $promotedVisibility = null,
        private readonly bool $promotedReadonly = false,
        private readonly array $attributes = []
    ) {
        parent::__construct(NodeKind::PARAMETER);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    public function type(): ?TypeNode
    {
        return $this->type;
    }

    public function passingMode(): ParameterPassingMode
    {
        return $this->passingMode;
    }

    public function isVariadic(): bool
    {
        return $this->variadic;
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->defaultValue;
    }

    public function promotedVisibility(): ?Visibility
    {
        return $this->promotedVisibility;
    }

    public function isPromotedReadonly(): bool
    {
        return $this->promotedReadonly;
    }

    /**
     * @return list<AttributeGroupNode>
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
        $children = $this->attributes;

        if ($this->type !== null) {
            $children[] = $this->type;
        }

        if ($this->defaultValue !== null) {
            $children[] = $this->defaultValue;
        }

        return $children;
    }
}
