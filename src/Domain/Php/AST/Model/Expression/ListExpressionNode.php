<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class ListExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ListItemNode> $items
     */
    public function __construct(
        private readonly array $items,
        private readonly ExpressionNode $value
    ) {
        parent::__construct(NodeKind::LIST_EXPRESSION);
    }

    /**
     * @return list<ListItemNode>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            ...$this->items,
            $this->value,
        ];
    }
}
