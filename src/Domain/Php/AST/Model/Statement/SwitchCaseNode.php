<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class SwitchCaseNode extends AbstractNode
{
    private bool $hasCondition;
    private int $statementsOffset;
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        ?ExpressionNode $condition,
        array $statements
    ) {
        parent::__construct();

        $this->hasCondition = $condition !== null;
        $this->statementsOffset = $this->hasCondition ? 1 : 0;

        if ($condition !== null) {
            $this->children()->add($condition);
        }

        foreach ($statements as $statement) {
            $this->children()->add($statement);
        }
    }

    public function condition(): ?ExpressionNode
    {
        return $this->hasCondition ? $this->children()->toArray()[0] : null;
    }

    public function isDefault(): bool
    {
        return $this->hasCondition === false;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return array_slice(
            $this->children()->toArray(),
            $this->statementsOffset,
        );
    }
}
