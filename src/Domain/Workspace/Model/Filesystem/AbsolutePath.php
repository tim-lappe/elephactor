<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model\Filesystem;

abstract class AbsolutePath
{
    /**
     * @var list<string>
     */
    private array $parts = [];

    public function __construct(string $value)
    {
        $this->parts = explode('/', trim($value, '/'));
    }

    public function equals(AbsolutePath $absolutePath): bool
    {
        if (count($this->parts) !== count($absolutePath->parts)) {
            return false;
        }

        for ($i = 0; $i < count($this->parts); $i++) {
            if ($this->parts[$i] !== $absolutePath->parts[$i]) {
                return false;
            }
        }

        return true;
    }

    public function startsWith(AbsolutePath $absolutePath): bool
    {
        if (count($this->parts) < count($absolutePath->parts)) {
            return false;
        }

        for ($i = 0; $i < count($absolutePath->parts); $i++) {
            if ($this->parts[$i] !== $absolutePath->parts[$i]) {
                return false;
            }
        }

        return true;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return '/' . implode('/', $this->parts);
    }
}
