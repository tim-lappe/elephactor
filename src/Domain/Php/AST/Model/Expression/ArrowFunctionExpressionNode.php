<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class ArrowFunctionExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<ParameterNode>      $parameters
     */
    public function __construct(
        array $attributes,
        array $parameters,
        ExpressionNode $body,
        private readonly ?TypeNode $returnType = null,
        private readonly bool $static = false,
        private readonly bool $returnsByReference = false
    ) {
        parent::__construct();

        foreach ($attributes as $attribute) {
            $this->children()->add('attribute', $attribute);
        }

        foreach ($parameters as $parameter) {
            $this->children()->add('parameter', $parameter);
        }

        $this->children()->add('body', $body);

        if ($this->returnType !== null) {
            $this->children()->add('returnType', $this->returnType);
        }
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->children()->getAllOf('attribute', AttributeGroupNode::class);
    }

    /**
     * @return list<ParameterNode>
     */
    public function parameters(): array
    {
        return $this->children()->getAllOf('parameter', ParameterNode::class);
    }

    public function body(): ExpressionNode
    {
        return $this->children()->getOne('body', ExpressionNode::class) ?? throw new \RuntimeException('Arrow function body not found');
    }

    public function returnType(): ?TypeNode
    {
        return $this->children()->getOne('returnType', TypeNode::class);
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function returnsByReference(): bool
    {
        return $this->returnsByReference;
    }

}
