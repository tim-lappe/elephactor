<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class UnsetStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<ExpressionNode> $expressions
     */
    public function __construct(
        private readonly array $expressions
    ) {
        if ($expressions === []) {
            throw new \InvalidArgumentException('Unset statement requires at least one expression');
        }

        parent::__construct(NodeKind::UNSET_STATEMENT);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function expressions(): array
    {
        return $this->expressions;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->expressions;
    }
}
