<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Index\ChainedFileIndex;
use TimLappe\Elephactor\Domain\Workspace\Index\FileIndex;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\ChainedClassLikeIndex;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\PhpClassLikeIndex;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\PhpFileIndex;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\ChainedPhpFileIndex;

final class Workspace
{
    private ChainedFileIndex $fileIndex;
    private ChainedClassLikeIndex $classIndex;
    private ChainedPhpFileIndex $phpFileIndex;

    public function __construct(
        private Directory $workspaceDirectory,
        private Environment $environment,
    ) {
        $this->fileIndex = new ChainedFileIndex([]);
        $this->classIndex = new ChainedClassLikeIndex([]);
        $this->phpFileIndex = new ChainedPhpFileIndex([]);
    }

    public function reloadIndices(): void
    {
        $this->fileIndex()->reload();
        $this->phpFileIndex()->reload();
        $this->classLikeIndex()->reload();
    }

    public function workspaceDirectory(): Directory
    {
        return $this->workspaceDirectory;
    }

    public function environment(): Environment
    {
        return $this->environment;
    }

    public function fileIndex(): ChainedFileIndex
    {
        return $this->fileIndex;
    }

    public function phpFileIndex(): ChainedPhpFileIndex
    {
        return $this->phpFileIndex;
    }

    public function classLikeIndex(): ChainedClassLikeIndex
    {
        return $this->classIndex;
    }

    public function registerFileIndex(FileIndex $fileIndex): void
    {
        $this->fileIndex->add($fileIndex);
    }

    public function registerClassLikeIndex(PhpClassLikeIndex $classIndex): void
    {
        $this->classIndex->add($classIndex);
    }

    public function registerPhpFileIndex(PhpFileIndex $phpFileIndex): void
    {
        $this->phpFileIndex->add($phpFileIndex);
    }
}
