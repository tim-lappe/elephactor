<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class TryStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode>   $tryStatements
     * @param list<CatchClauseNode> $catchClauses
     */
    public function __construct(
        private readonly array $tryStatements,
        private readonly array $catchClauses = [],
        private readonly ?FinallyClauseNode $finallyClause = null
    ) {
        parent::__construct(NodeKind::TRY_STATEMENT);
    }

    /**
     * @return list<StatementNode>
     */
    public function tryStatements(): array
    {
        return $this->tryStatements;
    }

    /**
     * @return list<CatchClauseNode>
     */
    public function catchClauses(): array
    {
        return $this->catchClauses;
    }

    public function finallyClause(): ?FinallyClauseNode
    {
        return $this->finallyClause;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            ...$this->tryStatements,
            ...$this->catchClauses,
        ];

        if ($this->finallyClause !== null) {
            $children[] = $this->finallyClause;
        }

        return $children;
    }
}
