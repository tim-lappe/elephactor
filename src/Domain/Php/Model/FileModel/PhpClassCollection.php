<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

final class PhpClassCollection
{
    /**
     * @param array<PhpClass> $classes
     */
    public function __construct(
        private array $classes = [],
    ) {
    }

    public function add(PhpClass $class): void
    {
        $this->classes[] = $class;
    }

    public function addAll(PhpClassCollection $classCollection): void
    {
        $this->classes = array_merge($this->classes, $classCollection->toArray());
    }

    /**
     * @return array<PhpClass>
     */
    public function toArray(): array
    {
        return $this->classes;
    }
}
