<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class UnsetStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<ExpressionNode> $expressions
     */
    public function __construct(
        array $expressions
    ) {
        if ($expressions === []) {
            throw new \InvalidArgumentException('Unset statement requires at least one expression');
        }

        parent::__construct();

        foreach ($expressions as $expression) {
            $this->children()->add('expression', $expression);
        }
    }

    /**
     * @return list<ExpressionNode>
     */
    public function expressions(): array
    {
        return $this->children()->getAllOf('expression', ExpressionNode::class);
    }
}
