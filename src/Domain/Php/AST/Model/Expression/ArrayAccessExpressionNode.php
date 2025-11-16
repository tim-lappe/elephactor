<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class ArrayAccessExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $array,
        private readonly ?ExpressionNode $offset = null
    ) {
        parent::__construct(NodeKind::ARRAY_ACCESS_EXPRESSION);
    }

    public function array(): ExpressionNode
    {
        return $this->array;
    }

    public function offset(): ?ExpressionNode
    {
        return $this->offset;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->array];

        if ($this->offset !== null) {
            $children[] = $this->offset;
        }

        return $children;
    }
}
