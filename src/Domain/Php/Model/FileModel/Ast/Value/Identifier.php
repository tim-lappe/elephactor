<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

final class Identifier
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);
        if ($normalized === '') {
            throw new \InvalidArgumentException('Identifier cannot be empty');
        }

        if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $normalized) !== 1) {
            throw new \InvalidArgumentException('Identifier must be a valid PHP identifier');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function rename(string $newName): void
    {
        $this->value = $newName;
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
