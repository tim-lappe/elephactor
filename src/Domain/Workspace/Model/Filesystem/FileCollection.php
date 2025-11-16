<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model\Filesystem;

final class FileCollection
{
    /**
     * @param array<File> $files
     */
    public function __construct(private array $files = [])
    {
    }

    public function add(File $file): void
    {
        $this->files[] = $file;
    }

    public function remove(File $file): void
    {
        $this->files = array_filter($this->files, fn (File $f) => $f !== $file);
    }

    public function filter(callable $callback): FileCollection
    {
        return new FileCollection(array_filter($this->files, $callback));
    }

    public function filterByExtension(string $extension): FileCollection
    {
        return $this->filter(fn (File $file): bool => str_ends_with(strtolower($file->name()), $extension));
    }

    /**
     * @return array<File>
     */
    public function toArray(): array
    {
        return $this->files;
    }

    public function first(callable $callback): ?File
    {
        foreach ($this->toArray() as $file) {
            if ($callback($file)) {
                return $file;
            }
        }

        return null;
    }

    public function count(): int
    {
        return count($this->files);
    }

    public function addAll(FileCollection $fileCollection): void
    {
        foreach ($fileCollection->toArray() as $file) {
            foreach ($this->files as $otherFile) {
                if ($file->equals($otherFile)) {
                    continue 2;
                }
            }

            $this->add($file);
        }
    }
}
