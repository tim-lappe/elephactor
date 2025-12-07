<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class TryStatementNode extends AbstractNode implements StatementNode
{
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

        foreach ($tryStatements as $tryStatement) {
            $this->children()->add('tryStatement', $tryStatement);
        }

        foreach ($catchClauses as $catchClause) {
            $this->children()->add('catchClause', $catchClause);
        }

        if ($finallyClause !== null) {
            $this->children()->add('finallyClause', $finallyClause);
        }
    }

    /**
     * @return list<StatementNode>
     */
    public function tryStatements(): array
    {
        return $this->children()->getAllOf('tryStatement', StatementNode::class);
    }

    /**
     * @return list<CatchClauseNode>
     */
    public function catchClauses(): array
    {
        return $this->children()->getAllOf('catchClause', CatchClauseNode::class);
    }

    public function finallyClause(): ?FinallyClauseNode
    {
        return $this->children()->getOne('finallyClause', FinallyClauseNode::class);
    }
}
