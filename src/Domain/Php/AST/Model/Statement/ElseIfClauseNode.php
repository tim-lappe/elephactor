<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class ElseIfClauseNode extends AbstractNode
{
    private int $statementsCount;
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        ExpressionNode $condition,
        array $statements
    ) {
        parent::__construct();

        $this->statementsCount = count($statements);

        $this->children()->add($condition);

        foreach ($statements as $statement) {
            $this->children()->add($statement);
        }
    }

    public function condition(): ExpressionNode
    {
        return $this->children()->toArray()[0] ?? throw new \RuntimeException('Else-if condition missing');
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return array_slice(
            $this->children()->toArray(),
            1,
            $this->statementsCount,
        );
    }
}
