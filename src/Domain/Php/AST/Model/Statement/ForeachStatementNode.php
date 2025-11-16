<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ForeachStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly ExpressionNode $source,
        private readonly ExpressionNode $value,
        private readonly ?ExpressionNode $key = null,
        private readonly bool $byReference = false,
        private readonly array $statements = []
    ) {
        parent::__construct(NodeKind::FOREACH_STATEMENT);
    }

    public function source(): ExpressionNode
    {
        return $this->source;
    }

    public function key(): ?ExpressionNode
    {
        return $this->key;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
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
        return $this->statements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            $this->source,
        ];

        if ($this->key !== null) {
            $children[] = $this->key;
        }

        $children[] = $this->value;

        return [
            ...$children,
            ...$this->statements,
        ];
    }
}
