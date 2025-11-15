<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class AliasMap
{
    /**
     * @param array<string, QualifiedName> $aliasMap
     */
    public function __construct(
        private array $aliasMap = [],
    ) {
    }

    public function add(Identifier $alias, QualifiedName $fullName): void
    {
        $this->aliasMap[strtolower($alias->value())] = $fullName;
    }

    public function merge(AliasMap $aliasMap): void
    {
        $this->aliasMap = array_merge($this->aliasMap, $aliasMap->aliasMap);
    }

    public function get(Identifier $alias): QualifiedName
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
