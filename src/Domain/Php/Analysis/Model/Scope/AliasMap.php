<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class AliasMap
{
    /**
     * @param array<string, FullyQualifiedName> $aliasMap
     */
    public function __construct(
        private array $aliasMap = [],
    ) {
    }

    public function add(Identifier $alias, FullyQualifiedName $fullName): void
    {
        $this->aliasMap[strtolower($alias->value())] = $fullName;
    }

    public function merge(AliasMap $aliasMap): void
    {
        $this->aliasMap = array_merge($this->aliasMap, $aliasMap->aliasMap);
    }

    public function get(Identifier $alias): FullyQualifiedName
    {
        if (!isset($this->aliasMap[strtolower($alias->value())])) {
            throw new \InvalidArgumentException(sprintf('Alias %s not found', $alias));
        }

        return $this->aliasMap[strtolower($alias->value())];
    }

    public function has(Identifier $alias): bool
    {
        return isset($this->aliasMap[strtolower($alias->value())]);
    }
}
