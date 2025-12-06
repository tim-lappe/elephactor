<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class ForeachStatementNode extends AbstractNode implements StatementNode
{
    private bool $hasKey;
    private int $statementsOffset;
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        ExpressionNode $source,
        ExpressionNode $value,
        ?ExpressionNode $key = null,
        private readonly bool $byReference = false,
        array $statements = []
    ) {
        parent::__construct();

        $this->hasKey = $key !== null;
        $this->statementsOffset = $this->hasKey ? 3 : 2;

        $this->children()->add($source);

        if ($key !== null) {
            $this->children()->add($key);
        }

        $this->children()->add($value);

        foreach ($statements as $statement) {
            $this->children()->add($statement);
        }
    }

    public function source(): ExpressionNode
    {
        return $this->children()->toArray()[0] ?? throw new \RuntimeException('Foreach source missing');
    }

    public function key(): ?ExpressionNode
    {
        return $this->hasKey ? $this->children()->toArray()[1] : null;
    }

    public function value(): ExpressionNode
    {
        return $this->children()->toArray()[$this->hasKey ? 2 : 1] ?? throw new \RuntimeException('Foreach value missing');
    }

    public function iteratesByReference(): bool
    {
        return $this->byReference;
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
