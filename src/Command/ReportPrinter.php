<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TimLappe\Elephactor\Domain\Php\Refactoring\FileRefactoringReport;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringReport;

final class ReportPrinter
{
    public function __construct(
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
    ) {
    }

    public function print(RefactoringReport $report): void
    {
        $io = new SymfonyStyle($this->input, $this->output);

        $fileReports = $report->fileRefactoringReports();
        if ($fileReports === []) {
            $io->warning('No refactorings to display.');
            return;
        }

        [$totalApplied, $totalSkipped] = $this->summarizeTotals($fileReports);

        $io->title('Refactoring report');
        $io->definitionList(
            ['Files processed' => (string) count($fileReports)],
            ['Applied refactorings' => (string) $totalApplied],
            ['Skipped refactorings' => (string) $totalSkipped],
        );

        $io->section('Files');
        $io->table(
            ['File', 'Applied', 'Skipped', 'Status'],
            array_map(fn (FileRefactoringReport $fileReport): array => $this->buildRow($fileReport), $fileReports),
        );

        $io->section('Details');
        foreach ($fileReports as $fileReport) {
            $filePath = $fileReport->file()->handle()->absolutePath()->value();
            $applied = $fileReport->refactoringResult()->appliedRefactorings();
            $unapplied = $fileReport->refactoringResult()->unappliedRefactorings();

            $io->writeln(sprintf('<info>%s</info>', $filePath));
            $io->text('Applied');
            $io->listing($applied !== [] ? $this->refactoringNames($applied) : ['None']);
            $io->text('Skipped');
            $io->listing($unapplied !== [] ? $this->refactoringNames($unapplied) : ['None']);
            $io->newLine();
        }
    }

    /**
     * @param list<FileRefactoringReport> $fileReports
     * @return array{0: int, 1: int}
     */
    private function summarizeTotals(array $fileReports): array
    {
        $totalApplied = 0;
        $totalSkipped = 0;

        foreach ($fileReports as $fileReport) {
            $result = $fileReport->refactoringResult();
            $totalApplied += count($result->appliedRefactorings());
            $totalSkipped += count($result->unappliedRefactorings());
        }

        return [$totalApplied, $totalSkipped];
    }

    /**
     * @return array{0: string, 1: string, 2: string, 3: string}
     */
    private function buildRow(FileRefactoringReport $fileReport): array
    {
        $result = $fileReport->refactoringResult();
        $appliedCount = count($result->appliedRefactorings());
        $skippedCount = count($result->unappliedRefactorings());
        $status = $appliedCount > 0 ? 'updated' : 'unchanged';

        return [
            $fileReport->file()->handle()->absolutePath()->value(),
            (string) $appliedCount,
            (string) $skippedCount,
            $status,
        ];
    }

    /**
     * @param list<object> $refactorings
     * @return list<string>
     */
    private function refactoringNames(array $refactorings): array
    {
        return array_map(
            static fn (object $refactoring): string => (new \ReflectionClass($refactoring))->getShortName(),
            $refactorings,
        );
    }
}