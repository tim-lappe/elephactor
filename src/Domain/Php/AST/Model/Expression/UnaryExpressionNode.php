<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\UnaryOperator;

final class UnaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly UnaryOperator $operator,
        ExpressionNode $operand
    ) {
        parent::__construct();

        $this->children()->add('operand', $operand);
    }

    public function operator(): UnaryOperator
    {
        return $this->operator;
    }

    public function operand(): ExpressionNode
    {
        return $this->children()->getOne('operand', ExpressionNode::class) ?? throw new \RuntimeException('Operand not found');
    }

}
