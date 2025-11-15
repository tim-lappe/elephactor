<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class IssetExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ExpressionNode> $expressions
     */
    public function __construct(
        private readonly array $expressions
    ) {
        if ($expressions === []) {
            throw new \InvalidArgumentException('Isset expression requires at least one operand');
        }

        parent::__construct(NodeKind::ISSET_EXPRESSION);
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
