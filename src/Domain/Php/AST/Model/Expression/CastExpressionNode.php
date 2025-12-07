<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\CastType;

final class CastExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly CastType $type,
        ExpressionNode $expression
    ) {
        parent::__construct();

        $this->children()->add('expression', $expression);
    }

    public function type(): CastType
    {
        return $this->type;
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->getOne('expression', ExpressionNode::class) ?? throw new \RuntimeException('Expression not found');
    }

}
