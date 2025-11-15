<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring\Executors;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClass;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringExecutor;
use TimLappe\Elephactor\Domain\Php\Repository\PhpFilePersister;
use TimLappe\Elephactor\Domain\Php\Resolution\ClassReference\ClassReferenceFinder;

final class ClassRenameExecutor implements RefactoringExecutor
{
    public function __construct(
        private readonly PhpFilePersister $phpFilePersister,
        private readonly ClassReferenceFinder $classReferenceFinder,
    ) {
    }

    public function supports(RefactoringCommand $command): bool
    {
        return $command instanceof ClassRename;
    }

    public function handle(RefactoringCommand $command): void
    {
        if (!$command instanceof ClassRename) {
            throw new \InvalidArgumentException('Command is not a ClassRename');
        }

        $phpClass = $command->phpClass();

        $this->renameUsageStatements($phpClass, $command->newName());
        $this->renameItself($phpClass, $command->newName());
    }

    private function renameItself(PhpClass $phpClass, Identifier $newName): void
    {
        $phpClass->file()->handle()->rename($newName->value() . '.php');
        $phpClass->changeIdentifier($newName);

        $this->phpFilePersister->persist($phpClass->file());
    }

    private function renameUsageStatements(PhpClass $phpClass, Identifier $newName): void
    {
        $references = $this->classReferenceFinder->findClassReferences($phpClass);
        foreach ($references as $reference) {
            foreach ($reference->referenceNodes() as $referenceNode) {
                $referenceNode->changeLastPart($newName);

                $this->phpFilePersister->persist($reference->file());
            }
        }
    }
}
