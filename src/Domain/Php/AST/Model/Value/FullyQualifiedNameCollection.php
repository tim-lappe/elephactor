<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

final class FullyQualifiedNameCollection
{
    /**
     * @param list<FullyQualifiedName> $fullyQualifiedNames
     */
    public function __construct(
        private array $fullyQualifiedNames = [],
    ) {}

    public function add(FullyQualifiedName $fullyQualifiedName): void
    {
        $this->fullyQualifiedNames[] = $fullyQualifiedName;
    }

    public function remove(FullyQualifiedName $fullyQualifiedName): void
    {
        $this->fullyQualifiedNames = array_values(array_filter($this->fullyQualifiedNames, fn (FullyQualifiedName $fullyQualifiedName) => !$fullyQualifiedName->equals($fullyQualifiedName)));
    }

    public function contains(FullyQualifiedName $fullyQualifiedName): bool
    {
        return array_filter($this->fullyQualifiedNames, fn (FullyQualifiedName $fullyQualifiedName) => $fullyQualifiedName->equals($fullyQualifiedName)) !== [];
    }

    /**
     * @return list<FullyQualifiedName>
     */
    public function toArray(): array
    {
        return $this->fullyQualifiedNames;
    }
}