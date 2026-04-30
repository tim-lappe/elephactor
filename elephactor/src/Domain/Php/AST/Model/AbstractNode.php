<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

abstract class AbstractNode implements Node
{
    /**
     * @var NodeCollection
     */
    private NodeCollection $children;

    private mixed $adapterNode;

    private int $leadingExtraNewlinesBefore = 0;

    public function __construct()
    {
        $this->children = new NodeCollection();
    }

    final public function leadingExtraNewlinesBefore(): int
    {
        return $this->leadingExtraNewlinesBefore;
    }

    final public function setLeadingExtraNewlinesBefore(int $count): void
    {
        $this->leadingExtraNewlinesBefore = max(0, $count);
    }

    final public function children(): NodeCollection
    {
        return $this->children;
    }

    final public function getAdapterNode(): mixed
    {
        return $this->adapterNode;
    }

    final public function setAdapterNode(mixed $node): void
    {
        $this->adapterNode = $node;
    }
}
