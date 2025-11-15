<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Resolution;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClass;

final class ClassFinder
{
    public function __construct(
        private readonly PhpClassCollection $classCollection,
    ) {
    }

    public function find(string $className): ?PhpClass
    {
        foreach ($this->classCollection->toArray() as $class) {
            if ($class->fullyQualifiedIdentifier()->containsName($className)) {
                return $class;
            }
        }

        return null;
    }
}
