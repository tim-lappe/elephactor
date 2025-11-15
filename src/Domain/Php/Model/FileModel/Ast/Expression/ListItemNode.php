<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

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
