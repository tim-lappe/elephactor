<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer;

final class RefactoringStack
{
    /**
     * @var list<Refactoring> $refactorings
     */
    private array $refactorings = [];

    public function add(Refactoring $refactoring): void
    {
        $this->refactorings[] = $refactoring;
    }
    
    public function apply(): void
    {
        foreach ($this->refactorings as $refactoring) {
            $refactoring->apply();
        }
    }
}