<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;

use function sprintf;

class RenameClass extends Command
{
    protected function configure(): void
    {
        $this->setName('class:rename')
            ->setDescription('Rename a class')
            ->addArgument('old-name', InputArgument::REQUIRED, 'The old name of the class')
            ->addArgument('new-name', InputArgument::REQUIRED, 'The new name of the class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $oldName = $input->getArgument('old-name');
        $newName = $input->getArgument('new-name');

        if (!is_string($oldName) || !is_string($newName)) {
            throw new \InvalidArgumentException('Old and new name must be strings');
        }

        $oldName = trim($oldName);
        $oldName = trim($oldName, '\\');
        $newName = trim($newName);
        $output->writeln(sprintf('Renaming class from %s to %s', $oldName, $newName));

        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new \RuntimeException('Application is not an instance of Application');
        }

        $class = $application->workspace()->classLikeIndex()->find(new ClassNameCriteria(new Identifier($oldName)))->first();
        if ($class === null) {
            throw new \RuntimeException(sprintf('Class %s not found', $oldName));
        }

        $refactoringExecutor = $application->refactoringExecutor();
        $refactoringExecutor->handle(new ClassRename($class, new Identifier($newName)));

        return Command::SUCCESS;
    }
}
