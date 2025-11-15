<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

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
