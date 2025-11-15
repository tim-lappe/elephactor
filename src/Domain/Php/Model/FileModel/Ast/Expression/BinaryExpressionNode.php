<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\BinaryOperator;

final class BinaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly BinaryOperator $operator,
        private readonly ExpressionNode $left,
        private readonly ExpressionNode $right
    ) {
        parent::__construct(NodeKind::BINARY_EXPRESSION);
    }

    public function operator(): BinaryOperator
    {
        return $this->operator;
    }

    public function left(): ExpressionNode
    {
        return $this->left;
    }

    public function right(): ExpressionNode
    {
        return $this->right;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->left,
            $this->right,
        ];
    }
}
