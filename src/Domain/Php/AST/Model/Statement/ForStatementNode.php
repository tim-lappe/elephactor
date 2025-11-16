<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
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
        private readonly array $initializers,
        private readonly array $conditions,
        private readonly array $loopExpressions,
        private readonly array $statements
    ) {
        parent::__construct(NodeKind::FOR_STATEMENT);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function initializers(): array
    {
        return $this->initializers;
    }

    /**
     * @return list<ExpressionNode>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return list<ExpressionNode>
     */
    public function loopExpressions(): array
    {
        return $this->loopExpressions;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->statements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            ...$this->initializers,
            ...$this->conditions,
            ...$this->loopExpressions,
            ...$this->statements,
        ];
    }
}
