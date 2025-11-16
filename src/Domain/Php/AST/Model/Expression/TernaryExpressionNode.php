<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class TernaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $condition,
        private readonly ?ExpressionNode $ifTrue,
        private readonly ExpressionNode $ifFalse
    ) {
        parent::__construct(NodeKind::TERNARY_EXPRESSION);
    }

    public function condition(): ExpressionNode
    {
        return $this->condition;
    }

    public function ifTrue(): ?ExpressionNode
    {
        return $this->ifTrue;
    }

    public function ifFalse(): ExpressionNode
    {
        return $this->ifFalse;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            $this->condition,
        ];

        if ($this->ifTrue !== null) {
            $children[] = $this->ifTrue;
        }

        $children[] = $this->ifFalse;

        return $children;
    }
}
