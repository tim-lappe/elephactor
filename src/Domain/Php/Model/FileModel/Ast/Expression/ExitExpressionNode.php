<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class ExitExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ?ExpressionNode $expression,
        private readonly bool $dieAlias = false
    ) {
        parent::__construct(NodeKind::EXIT_EXPRESSION);
    }

    public function expression(): ?ExpressionNode
    {
        return $this->expression;
    }

    public function usesDieAlias(): bool
    {
        return $this->dieAlias;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->expression !== null ? [$this->expression] : [];
    }
}
