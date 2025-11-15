<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

final class Psr4NamespaceSegmentIdentifier
{
    public function __construct(
        private string $name,
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Name cannot be empty');
        }

        if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name) !== 1) {
            throw new \InvalidArgumentException('Name must be a valid PHP identifier');
        }
    }

    public function name(): string
    {
        return $this->name;
    }
}
