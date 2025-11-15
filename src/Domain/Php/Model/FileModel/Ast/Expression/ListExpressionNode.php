<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

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
