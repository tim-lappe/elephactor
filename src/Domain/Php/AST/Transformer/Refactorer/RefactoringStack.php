<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer;

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

    public function apply(): RefactoringResult
    {
        /** @var list<Refactoring> $appliedRefactorings */
        $appliedRefactorings = [];
        /** @var list<Refactoring> $unappliedRefactorings */
        $unappliedRefactorings = [];

        foreach ($this->refactorings as $refactoring) {
            if (!$refactoring->isApplicable()) {
                $unappliedRefactorings[] = $refactoring;
                continue;
            }

            $refactoring->apply();
            $appliedRefactorings[] = $refactoring;
        }

        return new RefactoringResult($appliedRefactorings, $unappliedRefactorings);
    }
}
