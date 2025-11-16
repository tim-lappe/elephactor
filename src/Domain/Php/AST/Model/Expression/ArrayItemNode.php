<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class ArrayItemNode extends AbstractNode
{
    public function __construct(
        private readonly ExpressionNode $value,
        private readonly ?ExpressionNode $key = null,
        private readonly bool $byReference = false,
        private readonly bool $unpack = false
    ) {
        parent::__construct(NodeKind::ARRAY_ITEM);
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

    public function key(): ?ExpressionNode
    {
        return $this->key;
    }

    public function byReference(): bool
    {
        return $this->byReference;
    }

    public function isUnpacked(): bool
    {
        return $this->unpack;
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
