<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class ArrowFunctionExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<ParameterNode>      $parameters
     */
    public function __construct(
        private readonly array $attributes,
        private readonly array $parameters,
        private readonly ExpressionNode $body,
        private readonly ?TypeNode $returnType = null,
        private readonly bool $static = false,
        private readonly bool $returnsByReference = false
    ) {
        parent::__construct(NodeKind::ARROW_FUNCTION_EXPRESSION);
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return list<ParameterNode>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function body(): ExpressionNode
    {
        return $this->body;
    }

    public function returnType(): ?TypeNode
    {
        return $this->returnType;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function returnsByReference(): bool
    {
        return $this->returnsByReference;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            ...$this->attributes,
            ...$this->parameters,
            $this->body,
        ];

        if ($this->returnType !== null) {
            $children[] = $this->returnType;
        }

        return $children;
    }
}
