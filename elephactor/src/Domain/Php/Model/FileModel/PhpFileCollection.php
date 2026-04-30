<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

final class PhpFileCollection
{
    /**
     * @param array<PhpFile> $files
     */
    public function __construct(
        private array $files = [],
    ) {
    }

    public function add(PhpFile $file): void
    {
        $this->files[] = $file;
    }

    public function addAll(PhpFileCollection $fileCollection): void
    {
        $this->files = array_merge($this->files, $fileCollection->toArray());
    }

    public function filter(callable $callback): PhpFileCollection
    {
        return new PhpFileCollection(array_filter($this->files, $callback));
    }

    public function first(): ?PhpFile
    {
        return $this->files[0] ?? null;
    }

    public function count(): int
    {
        return count($this->files);
    }

    /**
     * @return array<PhpFile>
     */
    public function toArray(): array
    {
        return $this->files;
    }
}
