<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring\Commands;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;

final class ClassRename implements RefactoringCommand
{
    public function __construct(
        private readonly PhpClassLike $class,
        private readonly Identifier $newClassIdentifier,
    ) {
    }

    public function phpClass(): PhpClassLike
    {
        return $this->class;
    }

    public function newName(): Identifier
    {
        return $this->newClassIdentifier;
    }
}
