<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class UnsetStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<ExpressionNode> $expressions
     */
    public function __construct(
        private readonly array $expressions
    ) {
        if ($expressions === []) {
            throw new \InvalidArgumentException('Unset statement requires at least one expression');
        }

        parent::__construct(NodeKind::UNSET_STATEMENT);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function expressions(): array
    {
        return $this->expressions;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->expressions;
    }
}
