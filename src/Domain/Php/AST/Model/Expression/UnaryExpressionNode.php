<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\UnaryOperator;

final class UnaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly UnaryOperator $operator,
        private readonly ExpressionNode $operand
    ) {
        parent::__construct(NodeKind::UNARY_EXPRESSION);
    }

    public function operator(): UnaryOperator
    {
        return $this->operator;
    }

    public function operand(): ExpressionNode
    {
        return $this->operand;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->operand];
    }
}
