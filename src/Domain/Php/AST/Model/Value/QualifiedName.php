<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

readonly class QualifiedName
{
    protected const RESERVED_TYPE_NAMES = [
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
    protected array $parts;

    /**
     * @param list<Identifier> $parts
     */
    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    public static function fromString(string $name): QualifiedName
    {
        return new QualifiedName(
            array_map(
                static fn (string $part): Identifier => new Identifier($part),
                array_values(array_filter(explode('\\', $name), static fn (string $part): bool => $part !== '')),
            ),
        );
    }

    /**
     * @return list<Identifier>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    public function lastPart(): Identifier
    {
        return $this->parts[count($this->parts) - 1];
    }

    public function extend(Identifier $identifier): QualifiedName
    {
        return new QualifiedName([...$this->parts, $identifier]);
    }

    public function changeLastPart(Identifier $identifier): QualifiedName
    {
        $count = count($this->parts);
        $parts = $this->parts;
        $parts[$count - 1] = $identifier;
        $parts = array_values($parts);

        return new QualifiedName($parts);
    }

    public function removeLastPart(): QualifiedName
    {
        return new QualifiedName(array_slice($this->parts, 0, -1));
    }

    public function removeFirstPart(): QualifiedName
    {
        return new QualifiedName(array_slice($this->parts, 1));
    }

    public function prepend(Identifier $identifier): QualifiedName
    {
        return new QualifiedName([$identifier, ...$this->parts]);
    }

    /**
     * @param array<Identifier> $parts
     */
    public function replaceParts(array $parts): QualifiedName
    {
        if ($parts === []) {
            throw new \InvalidArgumentException('Qualified name requires at least one identifier');
        }

        $parts = array_values($parts);

        return new QualifiedName($parts);
    }

    public function __toString(): string
    {
        return implode('\\', array_map(
            static fn (Identifier $identifier): string => $identifier->value(),
            $this->parts,
        ));
    }

    public function isReservedTypeName(): bool
    {
        return in_array(strtolower($this->lastPart()->value()), self::RESERVED_TYPE_NAMES, true);
    }

    public function equals(QualifiedName $other): bool
    {
        if (count($this->parts) !== count($other->parts)) {
            return false;
        }

        for ($i = 0; $i < count($this->parts); $i++) {
            if (!$this->parts[$i]->equals($other->parts[$i])) {
                return false;
            }
        }

        return true;
    }

    public function startsWith(QualifiedName $qualifiedName): bool
    {
        if (count($this->parts) < count($qualifiedName->parts)) {
            return false;
        }

        for ($i = 0; $i < count($qualifiedName->parts); $i++) {
            if (!$this->parts[$i]->equals($qualifiedName->parts[$i])) {
                return false;
            }
        }

        return true;
    }

    public function endsWith(QualifiedName $qualifiedName): bool
    {
        if (count($this->parts) < count($qualifiedName->parts)) {
            return false;
        }

        for ($i = 0; $i < count($qualifiedName->parts); $i++) {
            if (!$this->parts[$i]->equals($qualifiedName->parts[$i])) {
                return false;
            }
        }

        return true;
    }
}
