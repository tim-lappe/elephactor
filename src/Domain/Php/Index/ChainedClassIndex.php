<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;

final class ChainedClassIndex implements PhpClassIndex
{
    /**
     * @param array<PhpClassIndex> $classIndexes
     */
    public function __construct(
        private array $classIndexes,
    ) {
    }

    public function reload(): void
    {
        foreach ($this->classIndexes as $classIndex) {
            $classIndex->reload();
        }
    }

    public function add(PhpClassIndex $classIndex): void
    {
        $this->classIndexes[] = $classIndex;
    }

    public function find(?PhpClassCriteria $criteria = null): PhpClassCollection
    {
        $matchingClasses = new PhpClassCollection([]);

        foreach ($this->classIndexes as $classIndex) {
            $matchingClasses->addAll($classIndex->find($criteria));
        }

        return $matchingClasses;
    }

    /**
     * @template T of PhpClassIndex
     * @param class-string<T> $className
     */
    public function getIndexForClass(string $className): ?PhpClassIndex
    {
        foreach ($this->classIndexes as $classIndex) {
            if ($classIndex instanceof $className) {
                return $classIndex;
            }
        }

        return null;
    }
}
