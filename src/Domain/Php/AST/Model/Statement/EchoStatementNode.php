<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class EchoStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<ExpressionNode> $expressions
     */
    public function __construct(
        array $expressions
    ) {
        if ($expressions === []) {
            throw new \InvalidArgumentException('Echo statement requires at least one expression');
        }

        parent::__construct();

        foreach ($expressions as $expression) {
            $this->children()->add($expression);
        }
    }

    /**
     * @return list<ExpressionNode>
     */
    public function expressions(): array
    {
        return $this->children()->filterTypeToArray(ExpressionNode::class);
    }
}
