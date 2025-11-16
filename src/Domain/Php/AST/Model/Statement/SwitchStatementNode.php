<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class SwitchStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<SwitchCaseNode> $cases
     */
    public function __construct(
        private readonly ExpressionNode $expression,
        private readonly array $cases
    ) {
        parent::__construct(NodeKind::SWITCH_STATEMENT);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    /**
     * @return list<SwitchCaseNode>
     */
    public function cases(): array
    {
        return $this->cases;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->expression,
            ...$this->cases,
        ];
    }
}
