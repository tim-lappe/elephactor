<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring;

interface RefactoringExecutor
{
    public function supports(RefactoringCommand $command): bool;

    public function handle(RefactoringCommand $command, bool $dryRun = false): RefactoringReport;
}
