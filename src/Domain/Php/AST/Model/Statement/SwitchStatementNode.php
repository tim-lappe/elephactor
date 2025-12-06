<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class SwitchStatementNode extends AbstractNode implements StatementNode
{
    private int $casesCount;
    /**
     * @param list<SwitchCaseNode> $cases
     */
    public function __construct(
        ExpressionNode $expression,
        array $cases
    ) {
        parent::__construct();

        $this->casesCount = count($cases);

        $this->children()->add($expression);

        foreach ($cases as $case) {
            $this->children()->add($case);
        }
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->toArray()[0] ?? throw new \RuntimeException('Switch expression missing');
    }

    /**
     * @return list<SwitchCaseNode>
     */
    public function cases(): array
    {
        return array_slice(
            $this->children()->toArray(),
            1,
            $this->casesCount,
        );
    }
}
