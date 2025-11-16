<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Argument;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\ParameterPassingMode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Visibility;

final class ParameterNode extends AbstractNode
{
    private IdentifierNode $name;
    /**
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        Identifier $name,
        private readonly ?TypeNode $type = null,
        private readonly ParameterPassingMode $passingMode = ParameterPassingMode::BY_VALUE,
        private readonly bool $variadic = false,
        private readonly ?ExpressionNode $defaultValue = null,
        private readonly ?Visibility $promotedVisibility = null,
        private readonly bool $promotedReadonly = false,
        private readonly array $attributes = []
    ) {
        parent::__construct(NodeKind::PARAMETER);

        $this->name = new IdentifierNode($name, $this);
    }

    public function name(): IdentifierNode
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
        $children = [
            $this->name,
            ...$this->attributes,
        ];

        if ($this->type !== null) {
            $children[] = $this->type;
        }

        if ($this->defaultValue !== null) {
            $children[] = $this->defaultValue;
        }

        return $children;
    }
}
