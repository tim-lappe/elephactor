<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final readonly class PhpNamespace
{
    /**
     * @var list<Identifier>
     */
    private readonly array $parts;

    public function __construct(
        string $name,
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Namespace name cannot be empty');
        }

        if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*/', $name) !== 1) {
            throw new \InvalidArgumentException('Namespace name must be a valid PHP identifier: ' . $name);
        }

        $exploded = explode('\\', trim($name, '\\'));

        try {
            $this->parts = array_map(
                static fn (string $part): Identifier => new Identifier($part),
                $exploded,
            );
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Namespace name must be a valid PHP identifier: ' . $name, 0, $e);
        }
    }

    public function name(): string
    {
        return implode('\\', array_map(fn (Identifier $identifier): string => $identifier->value(), $this->parts));
    }

    /**
     * @return list<Identifier>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    public function equals(PhpNamespace $namespace): bool
    {
        return $this->name() === $namespace->name();
    }

    public function contains(PhpNamespace $namespace): bool
    {
        if (count($this->parts) > count($namespace->parts())) {
            return false;
        }

        foreach ($this->parts as $key => $part) {
            if (!isset($namespace->parts()[$key]) || !$namespace->parts()[$key]->equals($part)) {
                return false;
            }
        }

        return true;
    }

    public function removeLastPart(): PhpNamespace
    {
        return new PhpNamespace(implode('\\', array_slice($this->parts(), 0, -1)));
    }

    public function removeFirstPart(): PhpNamespace
    {
        return new PhpNamespace(implode('\\', array_slice($this->parts(), 1)));
    }

    public function extend(string $segment): PhpNamespace
    {
        $segment = trim($segment, '\\');
        if ($segment === '') {
            return $this;
        }

        return new PhpNamespace($this->name() . '\\' . $segment);
    }

    public function prepend(string $segment): PhpNamespace
    {
        $segment = trim($segment, '\\');
        if ($segment === '') {
            return $this;
        }

        return new PhpNamespace($segment . '\\' . $this->name());
    }

    public function preprendNamespace(PhpNamespace $namespace): PhpNamespace
    {
        return new PhpNamespace(implode('\\', array_merge($namespace->parts(), $this->parts)));
    }
}
