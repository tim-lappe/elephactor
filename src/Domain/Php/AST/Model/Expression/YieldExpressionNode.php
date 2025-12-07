<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class YieldExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ?ExpressionNode $value,
        ?ExpressionNode $key = null
    ) {
        parent::__construct();

        if ($key !== null) {
            $this->children()->add('key', $key);
        }

        if ($value !== null) {
            $this->children()->add('value', $value);
        }
    }

    public function value(): ?ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class);
    }

    public function key(): ?ExpressionNode
    {
        return $this->children()->getOne('key', ExpressionNode::class);
    }

}
