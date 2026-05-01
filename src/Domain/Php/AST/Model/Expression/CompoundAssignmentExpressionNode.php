<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\AssignmentOperator;

final class CompoundAssignmentExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly AssignmentOperator $operator,
        ExpressionNode $target,
        ExpressionNode $value
    ) {
        if ($operator === AssignmentOperator::ASSIGN) {
            throw new \InvalidArgumentException('Compound assignment requires a compound operator');
        }

        parent::__construct();

        $this->children()->add('target', $target);
        $this->children()->add('value', $value);
    }

    public function operator(): AssignmentOperator
    {
        return $this->operator;
    }

    public function target(): ExpressionNode
    {
        return $this->children()->getOne('target', ExpressionNode::class) ?? throw new \RuntimeException('Target expression not found');
    }

    public function value(): ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class) ?? throw new \RuntimeException('Value expression not found');
    }

}
