<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
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
        private readonly array $attributes,
        private readonly array $parameters,
        private readonly array $uses,
        private readonly array $bodyStatements,
        private readonly ?TypeNode $returnType = null,
        private readonly bool $static = false,
        private readonly bool $returnsByReference = false
    ) {
        parent::__construct(NodeKind::CLOSURE_EXPRESSION);
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

    /**
     * @return list<ClosureUseVariableNode>
     */
    public function uses(): array
    {
        return $this->uses;
    }

    /**
     * @return list<StatementNode>
     */
    public function bodyStatements(): array
    {
        return $this->bodyStatements;
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
            ...$this->uses,
            ...$this->bodyStatements,
        ];

        if ($this->returnType !== null) {
            $children[] = $this->returnType;
        }

        return $children;
    }
}
