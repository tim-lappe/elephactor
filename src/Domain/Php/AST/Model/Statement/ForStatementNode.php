<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class ForStatementNode extends AbstractNode implements StatementNode
{
    private int $initializersCount;
    private int $conditionsCount;
    private int $loopExpressionsCount;
    /**
     * @param list<ExpressionNode> $initializers
     * @param list<ExpressionNode> $conditions
     * @param list<ExpressionNode> $loopExpressions
     * @param list<StatementNode>  $statements
     */
    public function __construct(
        array $initializers,
        array $conditions,
        array $loopExpressions,
        array $statements
    ) {
        parent::__construct();

        $this->initializersCount = count($initializers);
        $this->conditionsCount = count($conditions);
        $this->loopExpressionsCount = count($loopExpressions);

        foreach ($initializers as $initializer) {
            $this->children()->add($initializer);
        }

        foreach ($conditions as $condition) {
            $this->children()->add($condition);
        }

        foreach ($loopExpressions as $loopExpression) {
            $this->children()->add($loopExpression);
        }

        foreach ($statements as $statement) {
            $this->children()->add($statement);
        }
    }

    /**
     * @return list<ExpressionNode>
     */
    public function initializers(): array
    {
        return array_slice(
            $this->children()->toArray(),
            0,
            $this->initializersCount,
        );
    }

    /**
     * @return list<ExpressionNode>
     */
    public function conditions(): array
    {
        return array_slice(
            $this->children()->toArray(),
            $this->initializersCount,
            $this->conditionsCount,
        );
    }

    /**
     * @return list<ExpressionNode>
     */
    public function loopExpressions(): array
    {
        return array_slice(
            $this->children()->toArray(),
            $this->initializersCount + $this->conditionsCount,
            $this->loopExpressionsCount,
        );
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return array_slice(
            $this->children()->toArray(),
            $this->initializersCount + $this->conditionsCount + $this->loopExpressionsCount,
        );
    }
}
