<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Repository;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;

final class ChainedClassProvider implements ClassProvider
{
    /**
     * @param array<ClassProvider> $classProviders
     */
    public function __construct(
        private readonly array $classProviders,
    ) {
    }

    public function provide(): PhpClassCollection
    {
        $classCollection = new PhpClassCollection([]);
        foreach ($this->classProviders as $classProvider) {
            $classCollection->addAll($classProvider->provide());
        }
        return $classCollection;
    }
}
