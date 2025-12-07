<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class ClosureExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<AttributeGroupNode>     $attributes
     * @param list<ParameterNode>          $parameters
     * @param list<ClosureUseVariableNode> $uses
     * @param list<StatementNode>          $bodyStatements
     */
    public function __construct(
        array $attributes,
        array $parameters,
        array $uses,
        array $bodyStatements,
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

        foreach ($uses as $use) {
            $this->children()->add('use', $use);
        }

        foreach ($bodyStatements as $statement) {
            $this->children()->add('bodyStatement', $statement);
        }

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

    /**
     * @return list<ClosureUseVariableNode>
     */
    public function uses(): array
    {
        return $this->children()->getAllOf('use', ClosureUseVariableNode::class);
    }

    /**
     * @return list<StatementNode>
     */
    public function bodyStatements(): array
    {
        return $this->children()->getAllOf('bodyStatement', StatementNode::class);
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
