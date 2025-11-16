<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Index;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;

final class ChainedFileIndex implements FileIndex
{
    /**
     * @param array<FileIndex> $indexes
     */
    public function __construct(
        private array $indexes = [],
    ) {
    }

    public function add(FileIndex $fileIndex): void
    {
        $this->indexes[] = $fileIndex;
    }

    public function find(?FileCriteria $criteria = null): FileCollection
    {
        foreach ($this->indexes as $index) {
            $files = $index->find($criteria);
            if ($files->count() > 0) {
                return $files;
            }
        }

        return new FileCollection([]);
    }

    public function reload(): void
    {
        foreach ($this->indexes as $index) {
            $index->reload();
        }
    }

    /**
     * @template T of FileIndex
     * @param class-string<T> $className
     */
    public function getIndexForClass(string $className): ?FileIndex
    {
        foreach ($this->indexes as $index) {
            if ($index instanceof $className) {
                return $index;
            }
        }

        return null;
    }
}
