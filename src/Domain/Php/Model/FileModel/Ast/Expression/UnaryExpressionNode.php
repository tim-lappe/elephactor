<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\UnaryOperator;

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
