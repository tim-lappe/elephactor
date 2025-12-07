<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ListExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ListItemNode> $items
     */
    public function __construct(
        array $items,
        ExpressionNode $value
    ) {
        parent::__construct();

        foreach ($items as $item) {
            $this->children()->add('item', $item);
        }

        $this->children()->add('value', $value);
    }

    /**
     * @return list<ListItemNode>
     */
    public function items(): array
    {
        return $this->children()->getAllOf('item', ListItemNode::class);
    }

    public function value(): ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class) ?? throw new \RuntimeException('Value expression not found');
    }

}
