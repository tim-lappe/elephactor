<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class ListItemNode extends AbstractNode
{
    public function __construct(
        private readonly ?ExpressionNode $key,
        private readonly ExpressionNode $value
    ) {
        parent::__construct(NodeKind::LIST_ITEM);
    }

    public function key(): ?ExpressionNode
    {
        return $this->key;
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
        $children = [];

        if ($this->key !== null) {
            $children[] = $this->key;
        }

        $children[] = $this->value;

        return $children;
    }
}
