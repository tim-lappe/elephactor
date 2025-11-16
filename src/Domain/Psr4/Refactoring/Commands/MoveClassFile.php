<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Refactoring\Commands;

use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

final class MoveClassFile implements RefactoringCommand
{
    public function __construct(
        private readonly Psr4ClassFile $classFile,
        private readonly Directory $newDirectory,
    ) {
    }

    public function psr4ClassFile(): Psr4ClassFile
    {
        return $this->classFile;
    }

    public function newDirectory(): Directory
    {
        return $this->newDirectory;
    }
}
