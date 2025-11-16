<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\FileIndex;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFileCollection;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria\PhpFileCriteria;

final class ChainedPhpFileIndex implements PhpFileIndex
{
    /**
     * @param array<PhpFileIndex> $indexes
     */
    public function __construct(
        private array $indexes = [],
    ) {
    }

    public function add(PhpFileIndex $fileIndex): void
    {
        $this->indexes[] = $fileIndex;
    }

    public function reload(): void
    {
        foreach ($this->indexes as $index) {
            $index->reload();
        }
    }

    /**
     * @template T of PhpFileIndex
     * @param class-string<T> $className
     */
    public function getIndexForClass(string $className): ?PhpFileIndex
    {
        foreach ($this->indexes as $index) {
            if ($index instanceof $className) {
                return $index;
            }
        }

        return null;
    }

    public function find(?PhpFileCriteria $criteria = null): PhpFileCollection
    {
        foreach ($this->indexes as $index) {
            $files = $index->find($criteria);
            if ($files->count() > 0) {
                return $files;
            }
        }

        return new PhpFileCollection([]);
    }
}
