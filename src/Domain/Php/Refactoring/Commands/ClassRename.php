<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring\Commands;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClass;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;

final class ClassRename implements RefactoringCommand
{
    public function __construct(
        private readonly PhpClass $class,
        private readonly Identifier $newClassIdentifier,
    ) {
    }

    public function phpClass(): PhpClass
    {
        return $this->class;
    }

    public function newName(): Identifier
    {
        return $this->newClassIdentifier;
    }
}
