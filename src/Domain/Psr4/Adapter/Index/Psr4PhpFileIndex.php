<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria\FilesContainNamespaceCriteria;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria\PhpFileCriteria;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\PhpFileIndex;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFileCollection;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4AutoloadMap;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria\PhpFileObjectCriteria;
use TimLappe\Elephactor\Domain\Php\Repository\PhpFileRepository;

final class Psr4PhpFileIndex implements PhpFileIndex
{
    private NamespaceFileMap $namespaceFileMap;

    public function __construct(
        private readonly Psr4AutoloadMap $autoloadMap,
        private readonly PhpFileRepository $phpFileRepository,
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

    public function resolveNamespaceForDirectory(Directory $directory): ?QualifiedName
    {
        return $this->autoloadMap->resolveNamespaceForDirectory($directory);
    }

    private function buildNamespaceFileMap(Directory $directory, QualifiedName $namespace): void
    {
        $phpFiles = $directory->childFiles()->filterByExtension('.php');

        foreach ($phpFiles->toArray() as $file) {
            $phpFile = $this->phpFileRepository->find($file);

            $this->namespaceFileMap->add($namespace, new PhpFileCollection([$phpFile]));
        }

        foreach ($directory->childDirectories()->toArray() as $childDirectory) {
            $childNamespace = $namespace->extend(new Identifier($childDirectory->name()));
            $this->buildNamespaceFileMap($childDirectory, $childNamespace);
        }
    }

    public function find(?PhpFileCriteria $criteria = null): PhpFileCollection
    {
        $matchingFiles = new PhpFileCollection();

        foreach ($this->namespaceFileMap->items() as $item) {
            if ($criteria === null) {
                $matchingFiles->addAll($item->files());
                continue;
            }

            if ($criteria instanceof FilesContainNamespaceCriteria) {
                if ($criteria->exactMatch() && $criteria->namespace()->equals($item->namespace())) {
                    $matchingFiles->addAll($item->files());
                }

                if (!$criteria->exactMatch() && $item->namespace()->startsWith($criteria->namespace())) {
                    $matchingFiles->addAll($item->files());
                }
            }

            if ($criteria instanceof PhpFileObjectCriteria) {
                foreach ($item->files()->toArray() as $indexedFile) {
                    if ($indexedFile->handle()->equals($criteria->file()->handle())) {
                        $matchingFiles->add($indexedFile);
                    }
                }
            }
        }

        return $matchingFiles;
    }
}
