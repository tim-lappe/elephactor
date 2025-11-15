<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

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
