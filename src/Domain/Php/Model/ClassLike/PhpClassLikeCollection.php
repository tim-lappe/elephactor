<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\ClassLike;

final class PhpClassLikeCollection
{
    /**
     * @param array<PhpClassLike> $classes
     */
    public function __construct(
        private array $classes = [],
    ) {
    }

    public function add(PhpClassLike $class): void
    {
        $this->classes[] = $class;
    }

    public function addAll(PhpClassLikeCollection $classCollection): void
    {
        $this->classes = array_merge($this->classes, $classCollection->toArray());
    }

    public function filter(callable $callback): PhpClassLikeCollection
    {
        return new PhpClassLikeCollection(array_filter($this->classes, $callback));
    }

    public function first(): ?PhpClassLike
    {
        return $this->classes[0] ?? null;
    }

    /**
     * @return array<PhpClassLike>
     */
    public function toArray(): array
    {
        return $this->classes;
    }
}
