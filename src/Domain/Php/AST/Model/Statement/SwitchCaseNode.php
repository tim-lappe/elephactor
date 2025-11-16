<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class SwitchCaseNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly ?ExpressionNode $condition,
        private readonly array $statements
    ) {
        parent::__construct(NodeKind::SWITCH_CASE);
    }

    public function condition(): ?ExpressionNode
    {
        return $this->condition;
    }

    public function isDefault(): bool
    {
        return $this->condition === null;
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
        $children = $this->statements;

        if ($this->condition !== null) {
            array_unshift($children, $this->condition);
        }

        return $children;
    }
}
