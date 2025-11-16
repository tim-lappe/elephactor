<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model;

enum PhpVersion: string
{
    case PHP_7_4 = '7.4';
    case PHP_8_0 = '8.0';
    case PHP_8_1 = '8.1';
    case PHP_8_2 = '8.2';
    case PHP_8_3 = '8.3';
    case PHP_8_4 = '8.4';

    public static function fromString(string $version): self
    {
        foreach (self::cases() as $case) {
            if (str_starts_with($version, $case->value)) {
                return $case;
            }
        }

        throw new \RuntimeException(sprintf('Unsupported PHP version: %s', $version));
    }

    public static function fromHost(): self
    {
        return self::fromString(phpversion());
    }
}
