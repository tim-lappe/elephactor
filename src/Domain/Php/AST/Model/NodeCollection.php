<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

final class NodeCollection
{
    /**
     * @var list<NodeCollectionItem> $nodes
     */
    private array $nodes = [];


    /**
     * @template T of Node
     * @param  string          $key
     * @param  class-string<T> $type
     * @return ?T
     */
    public function getOne(string $key, string $type): ?Node
    {
        $node = array_values(array_filter($this->nodes, fn (NodeCollectionItem $item) => $item->key() === $key))[0] ?? null;
        if ($node === null) {
            return null;
        }

        $node = $node->node();

        if (!$node instanceof $type) {
            return null;
        }

        return $node;
    }

    /**
     * @template T of Node
     * @param  string          $key
     * @param  class-string<T> $type
     * @return list<T>
     */
    public function getAllOf(string $key, string $type): array
    {
        /** @var list<T> $array */
        $array = array_map(fn (NodeCollectionItem $item) => $item->node(), array_values(array_filter($this->nodes, fn (NodeCollectionItem $item) => $item->key() === $key)));

        return array_values(array_filter($array, fn (Node $node) => $node instanceof $type));
    }

    /**
     * @template T of Node
     * @param  class-string<T> $type
     * @return list<T>
     */
    public function getAllOfNestedByType(string $type): array
    {
        $result = [];
        foreach ($this->nodes as $node) {
            if ($node->node() instanceof $type) {
                $result[] = $node->node();
            }
            $result = array_merge($result, $node->node()->children()->getAllOfNestedByType($type));
        }

        return $result;
    }

    public function add(string $key, Node $node): void
    {
        $this->nodes[] = new NodeCollectionItem($key, $node);
    }

    public function addAll(NodeCollection $nodeCollection): void
    {
        foreach ($nodeCollection->nodes as $node) {
            $this->add($node->key(), $node->node());
        }
    }


    /**
     * @param  class-string<Node> $type
     * @return self
     */
    public function filterType(string $type): NodeCollection
    {
        $newCollection = new NodeCollection();
        foreach ($this->nodes as $node) {
            if ($node->node() instanceof $type) {
                $newCollection->add($node->key(), $node->node());
            }
        }
        return $newCollection;
    }

    /**
     * @template T of Node
     * @param  class-string<T> $type
     * @return list<T>
     */
    public function filterTypeToArray(string $type): array
    {
        /** @var list<T> $array */
        $array = $this->filterType($type)->toArray();
        return $array;
    }

    /**
     * @template T of Node
     * @param  class-string<T> $type
     * @return ?T
     */
    public function firstOfType(string $type): ?Node
    {
        foreach ($this->nodes as $node) {
            if ($node->node() instanceof $type) {
                return $node->node();
            }
        }

        return null;
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    /**
     * @return list<Node>
     */
    public function toArray(): array
    {
        return array_map(fn (NodeCollectionItem $item) => $item->node(), $this->nodes);
    }

    public function remove(string $key): void
    {
        $this->nodes = array_values(array_filter(
            $this->nodes,
            fn (NodeCollectionItem $item): bool => $item->key() !== $key,
        ));
    }
}
