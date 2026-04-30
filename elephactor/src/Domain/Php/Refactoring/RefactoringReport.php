<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring;

final class RefactoringReport
{
    /**
     * @param list<FileRefactoringReport> $fileRefactoringReports
     */
    public function __construct(
        private array $fileRefactoringReports = [],
    ) {
    }

    /**
     * @return list<FileRefactoringReport>
     */
    public function fileRefactoringReports(): array
    {
        return $this->fileRefactoringReports;
    }

    public function addFileRefactoringReport(FileRefactoringReport $fileRefactoringReport): void
    {
        $this->fileRefactoringReports[] = $fileRefactoringReport;
    }
}