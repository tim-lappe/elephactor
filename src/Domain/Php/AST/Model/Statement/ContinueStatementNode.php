<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ContinueStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly ?ExpressionNode $levels = null
    ) {
        parent::__construct(NodeKind::CONTINUE_STATEMENT);
    }

    public function levels(): ?ExpressionNode
    {
        return $this->levels;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->levels !== null ? [$this->levels] : [];
    }
}
