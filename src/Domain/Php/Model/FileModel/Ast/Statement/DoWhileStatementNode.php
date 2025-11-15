<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class DoWhileStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly ExpressionNode $condition,
        private readonly array $statements
    ) {
        parent::__construct(NodeKind::DO_WHILE_STATEMENT);
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
