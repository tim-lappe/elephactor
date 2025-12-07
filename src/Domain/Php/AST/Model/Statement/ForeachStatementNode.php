<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ForeachStatementNode extends AbstractNode implements StatementNode
{
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

        $this->children()->add('source', $source);

        if ($key !== null) {
            $this->children()->add('key', $key);
        }

        $this->children()->add('value', $value);

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    public function source(): ExpressionNode
    {
        return $this->children()->getOne('source', ExpressionNode::class) ?? throw new \RuntimeException('Foreach source missing');
    }

    public function key(): ?ExpressionNode
    {
        return $this->children()->getOne('key', ExpressionNode::class);
    }

    public function value(): ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class) ?? throw new \RuntimeException('Foreach value missing');
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
        return $this->children()->getAllOf('statement', StatementNode::class);
    }
}
