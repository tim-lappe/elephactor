<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\LiteralValue;

final class LiteralExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly LiteralValue $value
    ) {
        parent::__construct(NodeKind::LITERAL_EXPRESSION);
    }

    public function value(): LiteralValue
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
