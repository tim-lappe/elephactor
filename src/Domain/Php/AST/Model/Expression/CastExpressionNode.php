<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\CastType;

final class CastExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly CastType $type,
        private readonly ExpressionNode $expression
    ) {
        parent::__construct(NodeKind::CAST_EXPRESSION);
    }

    public function type(): CastType
    {
        return $this->type;
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
