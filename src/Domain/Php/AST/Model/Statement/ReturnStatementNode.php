<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ReturnStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly ?ExpressionNode $expression
    ) {
        parent::__construct(NodeKind::RETURN_STATEMENT);
    }

    public function expression(): ?ExpressionNode
    {
        return $this->expression;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->expression !== null ? [$this->expression] : [];
    }
}
