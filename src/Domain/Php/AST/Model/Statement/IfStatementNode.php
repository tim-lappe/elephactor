<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class IfStatementNode extends AbstractNode implements StatementNode
{
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

        $this->children()->add('condition', $condition);

        foreach ($ifStatements as $ifStatement) {
            $this->children()->add('ifStatement', $ifStatement);
        }

        foreach ($elseIfClauses as $elseIfClause) {
            $this->children()->add('elseIfClause', $elseIfClause);
        }

        if ($elseClause !== null) {
            $this->children()->add('elseClause', $elseClause);
        }
    }

    public function condition(): ExpressionNode
    {
        return $this->children()->getOne('condition', ExpressionNode::class) ?? throw new \RuntimeException('If statement missing condition');
    }

    /**
     * @return list<StatementNode>
     */
    public function ifStatements(): array
    {
        return $this->children()->getAllOf('ifStatement', StatementNode::class);
    }

    /**
     * @return list<ElseIfClauseNode>
     */
    public function elseIfClauses(): array
    {
        return $this->children()->getAllOf('elseIfClause', ElseIfClauseNode::class);
    }

    public function elseClause(): ?ElseClauseNode
    {
        return $this->children()->getOne('elseClause', ElseClauseNode::class);
    }
}
