<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring\Commands;

use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

final class MoveFile implements RefactoringCommand
{
    public function __construct(
        private readonly PhpFile $file,
        private readonly Directory $newDirectory,
    ) {
    }

    public function phpFile(): PhpFile
    {
        return $this->file;
    }

    public function newDirectory(): Directory
    {
        return $this->newDirectory;
    }
}
