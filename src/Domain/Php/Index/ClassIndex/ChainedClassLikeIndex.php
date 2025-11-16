<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\ClassIndex;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\PhpClassLikeCriteria;
use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLikeCollection;

final class ChainedClassLikeIndex implements PhpClassLikeIndex
{
    /**
     * @param array<PhpClassLikeIndex> $classIndexes
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

    public function add(PhpClassLikeIndex $classIndex): void
    {
        $this->classIndexes[] = $classIndex;
    }

    public function find(?PhpClassLikeCriteria $criteria = null): PhpClassLikeCollection
    {
        $matchingClasses = new PhpClassLikeCollection([]);

        foreach ($this->classIndexes as $classIndex) {
            $matchingClasses->addAll($classIndex->find($criteria));
        }

        return $matchingClasses;
    }

    /**
     * @template T of PhpClassLikeIndex
     * @param class-string<T> $className
     */
    public function getIndexForClass(string $className): ?PhpClassLikeIndex
    {
        foreach ($this->classIndexes as $classIndex) {
            if ($classIndex instanceof $className) {
                return $classIndex;
            }
        }

        return null;
    }
}
