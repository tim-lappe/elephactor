<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Traversal;

final class VisitorContext
{
    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @template T of mixed
     * @param  string          $key
     * @param  class-string<T> $type
     * @return T
     */
    public function get(string $key, ?string $type = null): mixed
    {
        $value = $this->context[$key] ?? null;
        if ($type !== null && !$value instanceof $type) {
            throw new \RuntimeException(sprintf('Value for key %s is not of type %s', $key, $type));
        }
        return $value;
    }

    /**
     * @param  string       $key
     * @return array<mixed>
     */
    public function getArray(string $key): array
    {
        $value = $this->context[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }
        return $value;
    }

    public function getBoolean(string $key): bool
    {
        $value = $this->context[$key] ?? null;
        if (!is_bool($value)) {
            return false;
        }
        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->context[$key]);
    }
}
