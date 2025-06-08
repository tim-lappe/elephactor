<?php

namespace TimLappe\Elephactor\Model;

final class NamespaceMap
{
    /**
     * @param array<string, NamespaceDefinition> $namespaceMap
     */
    public function __construct(
        private array $namespaceMap,
    ) {
    }

    public function get(string $namespace): ?NamespaceDefinition
    {
        foreach ($this->namespaceMap as $namespaceDefinition) {
            if ($namespaceDefinition->contains($namespace)) {
                return $namespaceDefinition;
            }
        }

        return null;
    }
}