<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ArrayExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArrayItemNode> $items
     */
    public function __construct(
        array $items,
        private readonly bool $shortSyntax = true
    ) {
        parent::__construct();

        foreach ($items as $item) {
            $this->children()->add('item', $item);
        }
    }

    /**
     * @return list<ArrayItemNode>
     */
    public function items(): array
    {
        return $this->children()->getAllOf('item', ArrayItemNode::class);
    }

    public function usesShortSyntax(): bool
    {
        return $this->shortSyntax;
    }

}
