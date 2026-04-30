<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

final class NodeCollectionItem
{
    public function __construct(
        public string $key,
        public Node $node,
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function node(): Node
    {
        return $this->node;
    }
}
