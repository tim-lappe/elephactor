<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class PrintExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $expression
    ) {
        parent::__construct(NodeKind::PRINT_EXPRESSION);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->expression];
    }
}
