<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class ThrowStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        ExpressionNode $expression
    ) {
        parent::__construct();

        $this->children()->add($expression);
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->firstOfType(ExpressionNode::class) ?? throw new \RuntimeException('Throw expression missing');
    }
}
