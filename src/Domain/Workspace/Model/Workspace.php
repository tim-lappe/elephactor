<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Index\ChainedFileIndex;
use TimLappe\Elephactor\Domain\Workspace\Index\FileIndex;
use TimLappe\Elephactor\Domain\Php\Index\ChainedClassIndex;
use TimLappe\Elephactor\Domain\Php\Index\PhpClassIndex;

final class Workspace
{
    private ChainedFileIndex $fileIndex;
    private ChainedClassIndex $classIndex;

    public function __construct(
        private Directory $workspaceDirectory,
        private Environment $environment,
    ) {
        $this->fileIndex = new ChainedFileIndex([]);
        $this->classIndex = new ChainedClassIndex([]);
    }

    public function reloadIndices(): void
    {
        $this->fileIndex()->reload();
        $this->classIndex()->reload();
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

    public function classIndex(): ChainedClassIndex
    {
        return $this->classIndex;
    }

    public function registerFileIndex(FileIndex $fileIndex): void
    {
        $this->fileIndex->add($fileIndex);
    }

    public function registerClassIndex(PhpClassIndex $classIndex): void
    {
        $this->classIndex->add($classIndex);
    }
}
