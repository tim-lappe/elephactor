<?php

namespace TimLappe\Elephactor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Model\ClassName;
use TimLappe\Elephactor\Model\ExistingClassName;
use TimLappe\Elephactor\Refactoring\ClassNameRefactor;

class RenameClass extends Command
{
    protected function configure(): void
    {
        $this->setName('rename-class')
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

        $output->writeln(sprintf('Renaming class from %s to %s', $oldName, $newName));

        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new \RuntimeException('Application is not an instance of Application');
        }

        $classLoader = $application->getClassLoader();
        $classDefinition = $classLoader->load(new ExistingClassName($oldName));
        
        $classNameRefactor = new ClassNameRefactor($application->getEnvironment());
        $classNameRefactor->rename($classDefinition, new ClassName($newName));

        return Command::SUCCESS;
    }
}