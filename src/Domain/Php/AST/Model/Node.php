<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

interface Node
{
    /**
     * @return NodeCollection
     */
    public function children(): NodeCollection;

    /**
     * @return mixed
     */
    public function getAdapterNode(): mixed;

    /**
     * @param mixed $node
     */
    public function setAdapterNode(mixed $node): void;
}
