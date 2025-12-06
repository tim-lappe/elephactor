<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class IfStatementNode extends AbstractNode implements StatementNode
{
    private int $ifStatementsCount;
    private int $elseIfClausesCount;
    private bool $hasElseClause;
    /**
     * @param list<StatementNode>    $ifStatements
     * @param list<ElseIfClauseNode> $elseIfClauses
     */
    public function __construct(
        ExpressionNode $condition,
        array $ifStatements,
        array $elseIfClauses = [],
        ?ElseClauseNode $elseClause = null
    ) {
        parent::__construct();

        $this->ifStatementsCount = count($ifStatements);
        $this->elseIfClausesCount = count($elseIfClauses);
        $this->hasElseClause = $elseClause !== null;

        $this->children()->add($condition);

        foreach ($ifStatements as $ifStatement) {
            $this->children()->add($ifStatement);
        }

        foreach ($elseIfClauses as $elseIfClause) {
            $this->children()->add($elseIfClause);
        }

        if ($elseClause !== null) {
            $this->children()->add($elseClause);
        }
    }

    public function condition(): ExpressionNode
    {
        return $this->children()->toArray()[0] ?? throw new \RuntimeException('If statement missing condition');
    }

    /**
     * @return list<StatementNode>
     */
    public function ifStatements(): array
    {
        return array_slice(
            $this->children()->toArray(),
            1,
            $this->ifStatementsCount,
        );
    }

    /**
     * @return list<ElseIfClauseNode>
     */
    public function elseIfClauses(): array
    {
        return array_slice(
            $this->children()->toArray(),
            1 + $this->ifStatementsCount,
            $this->elseIfClausesCount,
        );
    }

    public function elseClause(): ?ElseClauseNode
    {
        if (!$this->hasElseClause) {
            return null;
        }

        $index = 1 + $this->ifStatementsCount + $this->elseIfClausesCount;

        return $this->children()->toArray()[$index] ?? null;
    }
}
