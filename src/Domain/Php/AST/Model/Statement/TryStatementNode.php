<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class TryStatementNode extends AbstractNode implements StatementNode
{
    private int $tryStatementsCount;
    private int $catchClausesCount;
    private bool $hasFinallyClause;
    /**
     * @param list<StatementNode>   $tryStatements
     * @param list<CatchClauseNode> $catchClauses
     */
    public function __construct(
        array $tryStatements,
        array $catchClauses = [],
        ?FinallyClauseNode $finallyClause = null
    ) {
        parent::__construct();

        $this->tryStatementsCount = count($tryStatements);
        $this->catchClausesCount = count($catchClauses);
        $this->hasFinallyClause = $finallyClause !== null;

        foreach ($tryStatements as $tryStatement) {
            $this->children()->add($tryStatement);
        }

        foreach ($catchClauses as $catchClause) {
            $this->children()->add($catchClause);
        }

        if ($finallyClause !== null) {
            $this->children()->add($finallyClause);
        }
    }

    /**
     * @return list<StatementNode>
     */
    public function tryStatements(): array
    {
        return array_slice(
            $this->children()->toArray(),
            0,
            $this->tryStatementsCount,
        );
    }

    /**
     * @return list<CatchClauseNode>
     */
    public function catchClauses(): array
    {
        return array_slice(
            $this->children()->toArray(),
            $this->tryStatementsCount,
            $this->catchClausesCount,
        );
    }

    public function finallyClause(): ?FinallyClauseNode
    {
        if (!$this->hasFinallyClause) {
            return null;
        }

        $index = $this->tryStatementsCount + $this->catchClausesCount;

        return $this->children()->toArray()[$index] ?? null;
    }
}
