<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

abstract class AbstractNode implements Node
{
    public function __construct(
        private readonly NodeKind $kind,
    ) {
    }

    final public function kind(): NodeKind
    {
        return $this->kind;
    }

    /**
     * @template T of Node
     *
     * @param  class-string<T> $class
     * @return list<T>
     */
    public function findChildrenOfType(string $class): array
    {
        $children = [];

        foreach ($this->children() as $child) {
            if ($child instanceof $class) {
                $children[] = $child;
            }
        }

        return $children;
    }

    /**
     * @template T of Node
     *
     * @param  class-string<T> $class
     * @return list<T>
     */
    public function findNestedChildrenOfType(string $class): array
    {
        $children = [];

        foreach ($this->children() as $child) {
            if ($child instanceof $class) {
                $children[] = $child;
            }

            if ($child instanceof AbstractNode) {
                $children = array_merge($children, $child->findNestedChildrenOfType($class));
            }
        }

        return $children;
    }
}
