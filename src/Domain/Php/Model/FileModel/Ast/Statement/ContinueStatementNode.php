<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

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
