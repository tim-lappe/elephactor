<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Argument;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\ParameterPassingMode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Visibility;

final readonly class ParameterNode extends AbstractNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        IdentifierNode $name,
        ?TypeNode $type = null,
        private readonly ParameterPassingMode $passingMode = ParameterPassingMode::BY_VALUE,
        private readonly bool $variadic = false,
        ?ExpressionNode $defaultValue = null,
        private readonly ?Visibility $promotedVisibility = null,
        private readonly bool $promotedReadonly = false,
        array $attributes = []
    ) {
        parent::__construct();

        $this->children()->add('name', $name);

        foreach ($attributes as $attribute) {
            $this->children()->add('attribute', $attribute);
        }

        if ($type !== null) {
            $this->children()->add('type', $type);
        }

        if ($defaultValue !== null) {
            $this->children()->add('defaultValue', $defaultValue);
        }
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne('name', IdentifierNode::class) ?? throw new \RuntimeException('Identifier not found');
    }

    public function type(): ?TypeNode
    {
        return $this->children()->getOne('type', TypeNode::class);
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
        return $this->children()->getOne('defaultValue', ExpressionNode::class);
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
        return $this->children()->getAllOf('attribute', AttributeGroupNode::class);
    }
}
