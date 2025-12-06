<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class DoWhileStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly ExpressionNode $condition,
        private readonly array $statements
    ) {
        parent::__construct();
    }

    public function condition(): ExpressionNode
    {
        return $this->condition;
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
            ...$this->statements,
            $this->condition,
        ];
    }
}
