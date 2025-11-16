<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4AutoloadMap;
use TimLappe\Elephactor\Domain\Workspace\Index\FileCriteria;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;
use TimLappe\Elephactor\Domain\Workspace\Index\FileIndex;

final class Psr4FileIndex implements FileIndex
{
    private NamespaceFileMap $namespaceFileMap;

    public function __construct(
        private readonly Psr4AutoloadMap $autoloadMap,
    ) {
        $this->namespaceFileMap = new NamespaceFileMap();
    }

    public function reload(): void
    {
        $this->namespaceFileMap = new NamespaceFileMap();

        foreach ($this->autoloadMap->getItems() as $item) {
            $this->buildNamespaceFileMap(
                $item->directory(),
                $item->namespace(),
            );
        }
    }

    public function autoloadMap(): Psr4AutoloadMap
    {
        return $this->autoloadMap;
    }

    public function namespaceFileMap(): NamespaceFileMap
    {
        return $this->namespaceFileMap;
    }

    private function buildNamespaceFileMap(Directory $directory, PhpNamespace $namespace): void
    {
        $phpFiles = $directory->childFiles()->filterByExtension('.php');
        foreach ($phpFiles->toArray() as $file) {
            $this->namespaceFileMap->add($namespace, $phpFiles);
        }

        foreach ($directory->childDirectories()->toArray() as $childDirectory) {
            $childNamespace = $namespace->extend($childDirectory->name());
            $this->buildNamespaceFileMap($childDirectory, $childNamespace);
        }
    }

    public function find(?FileCriteria $criteria = null): FileCollection
    {
        $matchingFiles = new FileCollection();

        foreach ($this->namespaceFileMap->items() as $item) {
            if ($criteria === null) {
                $matchingFiles->addAll($item->files());
                continue;
            }

            if ($criteria instanceof FilesInNamespaceCriteria) {
                if ($criteria->exactMatch() && $criteria->namespace()->equals($item->namespace())) {
                    $matchingFiles->addAll($item->files());
                } elseif (!$criteria->exactMatch() && $criteria->namespace()->contains($item->namespace())) {
                    $matchingFiles->addAll($item->files());
                }
            }
        }

        return $matchingFiles;
    }
}
