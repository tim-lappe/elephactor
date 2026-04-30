<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ForStatementNode extends AbstractNode implements StatementNode
{
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

        foreach ($initializers as $initializer) {
            $this->children()->add('initializer', $initializer);
        }

        foreach ($conditions as $condition) {
            $this->children()->add('condition', $condition);
        }

        foreach ($loopExpressions as $loopExpression) {
            $this->children()->add('loopExpression', $loopExpression);
        }

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    /**
     * @return list<ExpressionNode>
     */
    public function initializers(): array
    {
        return $this->children()->getAllOf('initializer', ExpressionNode::class);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function conditions(): array
    {
        return $this->children()->getAllOf('condition', ExpressionNode::class);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function loopExpressions(): array
    {
        return $this->children()->getAllOf('loopExpression', ExpressionNode::class);
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->children()->getAllOf('statement', StatementNode::class);
    }
}
