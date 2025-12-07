<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring;

use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\RefactoringResult;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

final class FileRefactoringReport
{
    public function __construct(
        private readonly PhpFile $file,
        private readonly RefactoringResult $refactoringResult,
    ) {
    }

    public function file(): PhpFile
    {
        return $this->file;
    }

    public function refactoringResult(): RefactoringResult
    {
        return $this->refactoringResult;
    }
}