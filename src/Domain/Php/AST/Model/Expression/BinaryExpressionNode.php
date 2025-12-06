<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\BinaryOperator;

final readonly class BinaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly BinaryOperator $operator,
        private readonly ExpressionNode $left,
        private readonly ExpressionNode $right
    ) {
        parent::__construct();
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
