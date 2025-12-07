<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

abstract class AbstractNode implements Node
{
    /**
     * @var NodeCollection
     */
    private NodeCollection $children;

    public function __construct()
    {
        $this->children = new NodeCollection();
    }

    final public function children(): NodeCollection
    {
        return $this->children;
    }
}
