<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final readonly class ExitExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ?ExpressionNode $expression,
        private readonly bool $dieAlias = false
    ) {
        parent::__construct();

        if ($this->expression !== null) {
            $this->children()->add($this->expression);
        }
    }

    public function expression(): ?ExpressionNode
    {
        return $this->expression;
    }

    public function usesDieAlias(): bool
    {
        return $this->dieAlias;
    }
}
