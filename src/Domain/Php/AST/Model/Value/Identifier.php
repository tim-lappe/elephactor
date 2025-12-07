<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

final class Identifier
{
    private string $value;

    public function __construct(string $value)
    {
        if (!self::valid($value)) {
            throw new \InvalidArgumentException('Identifier must be a valid PHP identifier. Got: ' . $value);
        }

        $this->value = trim($value);
    }

    public static function valid(string $value): bool
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return false;
        }

        if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $normalized) !== 1) {
            return false;
        }

        return true;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function createRenamed(string $newName): Identifier
    {
        return new Identifier($newName);
    }

    public function equals(string|self $other): bool
    {
        if (is_string($other)) {
            return $this->value === $other;
        }

        return strtolower($this->value) === strtolower($other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
