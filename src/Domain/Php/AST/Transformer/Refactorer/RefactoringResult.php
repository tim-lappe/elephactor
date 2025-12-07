<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer;

final class RefactoringResult
{
    /**
     * @param list<Refactoring> $appliedRefactorings
     * @param list<Refactoring> $unappliedRefactorings
     */
    public function __construct(
        private array $appliedRefactorings = [],
        private array $unappliedRefactorings = [],
    ) {
    }

    public function merge(RefactoringResult $other): void
    {
        $this->appliedRefactorings = array_merge($this->appliedRefactorings, $other->appliedRefactorings());
        $this->unappliedRefactorings = array_merge($this->unappliedRefactorings, $other->unappliedRefactorings());
    }

    /**
     * @return list<Refactoring>
     */
    public function appliedRefactorings(): array
    {
        return $this->appliedRefactorings;
    }

    /**
     * @return list<Refactoring>
     */
    public function unappliedRefactorings(): array
    {
        return $this->unappliedRefactorings;
    }
}
