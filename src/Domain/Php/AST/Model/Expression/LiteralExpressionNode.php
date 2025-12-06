<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\LiteralValue;

final readonly class LiteralExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly LiteralValue $value
    ) {
        parent::__construct();
    }

    public function value(): LiteralValue
    {
        return $this->value;
    }
}
