<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AliasMap;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;

final class QualifiedName
{
    private const RESERVED_TYPE_NAMES = [
        'array',
        'bool',
        'boolean',
        'callable',
        'double',
        'false',
        'float',
        'int',
        'integer',
        'iterable',
        'mixed',
        'never',
        'null',
        'object',
        'parent',
        'real',
        'resource',
        'scalar',
        'self',
        'static',
        'string',
        'true',
        'void',
    ];

    /**
     * @var list<Identifier>
     */
    private array $parts = [];

    private readonly bool $fullyQualified;

    private readonly bool $relative;

    /**
     * @param list<Identifier> $parts
     */
    public function __construct(array $parts, bool $fullyQualified = false, bool $relative = false)
    {
        if ($parts === []) {
            throw new \InvalidArgumentException('Qualified name requires at least one identifier');
        }

        $this->parts = $parts;
        $this->fullyQualified = $fullyQualified;
        $this->relative = $relative;
    }

    /**
     * @return list<Identifier>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    public function isFullyQualified(): bool
    {
        return $this->fullyQualified;
    }

    public function isRelative(): bool
    {
        return $this->relative;
    }

    public function lastPart(): Identifier
    {
        return $this->parts[count($this->parts) - 1];
    }

    public function changeLastPart(Identifier $identifier): void
    {
        $count = count($this->parts);
        $parts = $this->parts;
        $parts[$count - 1] = $identifier;

        $this->parts = array_values($parts);
    }

    public function __toString(): string
    {
        $prefix = '';

        if ($this->fullyQualified) {
            $prefix = '\\';
        } elseif ($this->relative) {
            $prefix = 'namespace\\';
        }

        return $prefix . implode('\\', array_map(
            static fn (Identifier $identifier): string => $identifier->value(),
            $this->parts,
        ));
    }

    /**
     * @param AliasMap $aliasMap
     */
    public function resolve(
        ?PhpNamespace $currentNamespace,
        AliasMap $aliasMap,
    ): FullyQualifiedName {
        if ($this->parts === []) {
            return new FullyQualifiedName([]);
        }

        if ($this->isFullyQualified()) {
            return new FullyQualifiedName($this->parts);
        }

        if ($this->isRelative()) {
            $namespaceParts = $currentNamespace !== null ? $currentNamespace->parts() : [];
            return new FullyQualifiedName([...$namespaceParts, ...$this->parts]);
        }

        $firstPart = $this->parts[0];
        if ($aliasMap->has($firstPart)) {
            $resolved = $aliasMap->get($firstPart);
            return new FullyQualifiedName([...$resolved->parts(), ...array_slice($this->parts, 1)]);
        }

        if ($currentNamespace !== null) {
            return new FullyQualifiedName([...$currentNamespace->parts(), ...$this->parts]);
        }

        return new FullyQualifiedName($this->parts);
    }

    public function isReservedTypeName(): bool
    {
        return in_array(strtolower($this->lastPart()->value()), self::RESERVED_TYPE_NAMES, true);
    }
}
