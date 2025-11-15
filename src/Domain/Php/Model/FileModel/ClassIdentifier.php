<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class ClassIdentifier
{
    public function __construct(
        private string $name,
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Class name cannot be empty');
        }
        if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name) !== 1) {
            throw new \InvalidArgumentException('Class name must be a valid PHP identifier');
        }
    }

    public function identifier(): Identifier
    {
        return new Identifier($this->name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function matchesStringIdentifier(string $name): bool
    {
        return strtolower($this->name()) === strtolower($name);
    }
}
