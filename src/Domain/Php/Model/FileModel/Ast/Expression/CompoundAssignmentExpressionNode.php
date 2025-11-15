<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\AssignmentOperator;

final class CompoundAssignmentExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly AssignmentOperator $operator,
        private readonly ExpressionNode $target,
        private readonly ExpressionNode $value
    ) {
        if ($operator === AssignmentOperator::ASSIGN) {
            throw new \InvalidArgumentException('Compound assignment requires a compound operator');
        }

        parent::__construct(NodeKind::COMPOUND_ASSIGNMENT_EXPRESSION);
    }

    public function operator(): AssignmentOperator
    {
        return $this->operator;
    }

    public function target(): ExpressionNode
    {
        return $this->target;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->target,
            $this->value,
        ];
    }
}
