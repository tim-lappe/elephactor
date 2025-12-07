<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class BreakStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        ?ExpressionNode $levels = null
    ) {
        parent::__construct();

        if ($levels !== null) {
            $this->children()->add('levels', $levels);
        }
    }

    public function levels(): ?ExpressionNode
    {
        return $this->children()->getOne('levels', ExpressionNode::class);
    }
}
