<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\PhpClassLikeCriteria;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\PhpClassLikeIndex;
use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLikeCollection;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4PhpFileIndex;

final class Psr4ClassLikeIndex implements PhpClassLikeIndex
{
    private PhpClassLikeCollection $phpClassCollection;

    public function __construct(
        private readonly Psr4PhpFileIndex $fileIndex,
    ) {
        $this->phpClassCollection = new PhpClassLikeCollection([]);
    }

    public function reload(): void
    {
        $this->phpClassCollection = new PhpClassLikeCollection([]);

        $namespaceFileMap = $this->fileIndex->namespaceFileMap();

        foreach ($namespaceFileMap->items() as $item) {
            foreach ($item->files()->toArray() as $phpFile) {
                $this->phpClassCollection->add(
                    new Psr4ClassFile($phpFile),
                );
            }
        }
    }

    public function find(?PhpClassLikeCriteria $criteria = null): PhpClassLikeCollection
    {
        $matchingClasses = new PhpClassLikeCollection([]);

        if ($criteria === null) {
            $matchingClasses->addAll($this->phpClassCollection);
            return $matchingClasses;
        }

        foreach ($this->phpClassCollection->toArray() as $phpClassLike) {
            if ($criteria->matches($phpClassLike)) {
                $matchingClasses->add($phpClassLike);
            }
        }

        return $matchingClasses;
    }
}
