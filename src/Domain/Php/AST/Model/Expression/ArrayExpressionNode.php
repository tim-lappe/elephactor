<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class ArrayExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArrayItemNode> $items
     */
    public function __construct(
        private readonly array $items,
        private readonly bool $shortSyntax = true
    ) {
        parent::__construct(NodeKind::ARRAY_EXPRESSION);
    }

    /**
     * @return list<ArrayItemNode>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function usesShortSyntax(): bool
    {
        return $this->shortSyntax;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->items;
    }
}
