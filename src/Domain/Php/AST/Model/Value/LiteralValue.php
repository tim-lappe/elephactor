<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

final class LiteralValue
{
    /**
     * @param string|int|float|bool|array<array-key, mixed>|null $value
     */
    private function __construct(
        private readonly LiteralKind $kind,
        /**
         * @var string|int|float|bool|array<array-key, mixed>|null
         */
        private readonly string|int|float|bool|array|null $value,
    ) {
    }

    public static function string(string $value): self
    {
        return new self(LiteralKind::STRING, $value);
    }

    public static function integer(int $value): self
    {
        return new self(LiteralKind::INTEGER, $value);
    }

    public static function float(float $value): self
    {
        return new self(LiteralKind::FLOAT, $value);
    }

    public static function boolean(bool $value): self
    {
        return new self(LiteralKind::BOOLEAN, $value);
    }

    public static function null(): self
    {
        return new self(LiteralKind::NULL, null);
    }

    /**
     * @param array<array-key, mixed> $value
     */
    public static function array(array $value): self
    {
        return new self(LiteralKind::ARRAY, $value);
    }

    public function kind(): LiteralKind
    {
        return $this->kind;
    }

    /**
     * @return string|int|float|bool|array<array-key, mixed>|null
     */
    public function value(): string|int|float|bool|array|null
    {
        return $this->value;
    }
}
