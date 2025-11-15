<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class IfStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode>    $ifStatements
     * @param list<ElseIfClauseNode> $elseIfClauses
     */
    public function __construct(
        private readonly ExpressionNode $condition,
        private readonly array $ifStatements,
        private readonly array $elseIfClauses = [],
        private readonly ?ElseClauseNode $elseClause = null
    ) {
        parent::__construct(NodeKind::IF_STATEMENT);
    }

    public function condition(): ExpressionNode
    {
        return $this->condition;
    }

    /**
     * @return list<StatementNode>
     */
    public function ifStatements(): array
    {
        return $this->ifStatements;
    }

    /**
     * @return list<ElseIfClauseNode>
     */
    public function elseIfClauses(): array
    {
        return $this->elseIfClauses;
    }

    public function elseClause(): ?ElseClauseNode
    {
        return $this->elseClause;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            $this->condition,
            ...$this->ifStatements,
            ...$this->elseIfClauses,
        ];

        if ($this->elseClause !== null) {
            $children[] = $this->elseClause;
        }

        return $children;
    }
}
