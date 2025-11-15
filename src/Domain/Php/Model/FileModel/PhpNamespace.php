<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class PhpNamespace
{
    /**
     * @var list<Identifier>
     */
    private array $parts = [];

    public function __construct(
        private string $name,
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Namespace name cannot be empty');
        }

        if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*/', $name) !== 1) {
            throw new \InvalidArgumentException('Namespace name must be a valid PHP identifier: ' . $name);
        }

        $exploded = explode('\\', trim($name, '\\'));
        $this->parts = array_map(
            static fn (string $part): Identifier => new Identifier($part),
            $exploded,
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return list<Identifier>
     */
    public function parts(): array
    {
        return $this->parts;
    }
}
