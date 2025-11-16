<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model\Filesystem;

final class DirectoryCollection
{
    /**
     * @param array<Directory> $directories
     */
    public function __construct(
        private array $directories = [],
    ) {
    }

    public function add(Directory $directory): void
    {
        $this->directories[] = $directory;
    }

    public function remove(Directory $directory): void
    {
        $this->directories = array_filter($this->directories, fn (Directory $d) => $d !== $directory);
    }

    public function find(string $name): ?Directory
    {
        foreach ($this->directories as $directory) {
            if ($directory->name() === $name) {
                return $directory;
            }
        }

        return null;
    }

    /**
     * @return array<Directory>
     */
    public function toArray(): array
    {
        return $this->directories;
    }

    public function first(callable $callback): ?Directory
    {
        foreach ($this->directories as $directory) {
            if ($callback($directory)) {
                return $directory;
            }
        }

        return null;
    }
}
