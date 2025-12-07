<?php

declare (strict_types=1);

namespace TimLappe\Elephactor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\MoveFile;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassFilePathCriteria;
use TimLappe\Elephactor\Adapter\Workspace\FsAbsolutePath;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

use function sprintf;

class MoveClass extends Command
{
    protected function configure(): void
    {
        $this->setName('class:move')
            ->setDescription('Move a class')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run the command')
            ->addArgument('class', InputArgument::REQUIRED, 'The path or class name of the class to move')
            ->addArgument('target-directory', InputArgument::REQUIRED, 'The target directory of the class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = $input->getOption('dry-run');
        if (!is_bool($dryRun)) {
            throw new \InvalidArgumentException('Dry run must be a boolean');
        }

        $classSource = $input->getArgument('class');
        $targetDirectoryStringPath = $input->getArgument('target-directory');

        if (!is_string($classSource) || !is_string($targetDirectoryStringPath)) {
            throw new \InvalidArgumentException('Class name and target directory must be strings');
        }

        $targetDirectory = trim($targetDirectoryStringPath);
        $targetDirectory = new FsAbsolutePath($targetDirectoryStringPath);

        $output->writeln(sprintf('Moving class %s to directory %s', $classSource, $targetDirectory));

        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new \RuntimeException('Application is not an instance of Application');
        }

        $class = $application->workspace()->classLikeIndex()->find(new ClassFilePathCriteria(new FsAbsolutePath($classSource)))->first();
        if ($class === null) {
            throw new \RuntimeException(sprintf('Class %s not found in workspace', $classSource));
        }

        if (Identifier::valid($classSource)) {
            $class = $application->workspace()->classLikeIndex()->find(new ClassNameCriteria($classSource))->first();
            if ($class === null) {
                throw new \RuntimeException(sprintf('Class %s not found in workspace', $classSource));
            }
        }

        $targetDirectory = $application->workspace()->workspaceDirectory()->find($targetDirectory);
        if (!$targetDirectory instanceof Directory) {
            throw new \RuntimeException(sprintf('Directory %s not found in workspace', $targetDirectoryStringPath));
        }

        $refactoringExecutor = $application->refactoringExecutor();
        $report = $refactoringExecutor->handle(new MoveFile($class->file(), $targetDirectory), $dryRun);
        $reportPrinter = new ReportPrinter($input, $output);
        $reportPrinter->print($report);

        return Command::SUCCESS;
    }
}
