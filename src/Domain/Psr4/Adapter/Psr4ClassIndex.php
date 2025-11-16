<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter;

use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Index\PhpClassCriteria;
use TimLappe\Elephactor\Domain\Php\Index\PhpClassIndex;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClass;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4FileIndex;

final class Psr4ClassIndex implements PhpClassIndex
{
    private PhpClassCollection $phpClassCollection;

    public function __construct(
        private readonly Psr4FileIndex $fileIndex,
        private readonly AstBuilder $astBuilder,
    ) {
        $this->phpClassCollection = new PhpClassCollection([]);
    }

    public function reload(): void
    {
        $this->phpClassCollection = new PhpClassCollection([]);

        $namespaceFileMap = $this->fileIndex->namespaceFileMap();

        foreach ($namespaceFileMap->items() as $item) {
            foreach ($item->files()->toArray() as $file) {
                $fileNode = $this->astBuilder->build($file->content());
                $phpFile = new PhpFile($file, $fileNode);
                $this->phpClassCollection->add(
                    new Psr4ClassFile($phpFile, $item->namespace()),
                );
            }
        }
    }

    public function find(?PhpClassCriteria $criteria = null): PhpClassCollection
    {
        $matchingClasses = new PhpClassCollection([]);

        if ($criteria === null) {
            $matchingClasses->addAll($this->phpClassCollection);
            return $matchingClasses;
        }

        if ($criteria instanceof ClassNameCriteria) {
            $matchingClasses->addAll(
                $this->phpClassCollection->filter(fn (PhpClass $phpClass) => $phpClass->fullyQualifiedIdentifier()->containsName($criteria->className()))
            );
        }

        return $matchingClasses;
    }
}
