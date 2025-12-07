<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ExitExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ?ExpressionNode $expression,
        private readonly bool $dieAlias = false
    ) {
        parent::__construct();

        if ($expression !== null) {
            $this->children()->add('expression', $expression);
        }
    }

    public function expression(): ?ExpressionNode
    {
        return $this->children()->getOne('expression', ExpressionNode::class);
    }

    public function usesDieAlias(): bool
    {
        return $this->dieAlias;
    }
}
