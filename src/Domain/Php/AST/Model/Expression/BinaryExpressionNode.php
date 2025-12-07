<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\BinaryOperator;

final class BinaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly BinaryOperator $operator,
        ExpressionNode $left,
        ExpressionNode $right
    ) {
        parent::__construct();

        $this->children()->add('left', $left);
        $this->children()->add('right', $right);
    }

    public function operator(): BinaryOperator
    {
        return $this->operator;
    }

    public function left(): ExpressionNode
    {
        return $this->children()->getOne('left', ExpressionNode::class) ?? throw new \RuntimeException('Left operand not found');
    }

    public function right(): ExpressionNode
    {
        return $this->children()->getOne('right', ExpressionNode::class) ?? throw new \RuntimeException('Right operand not found');
    }

}
