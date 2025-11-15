<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Command\Debug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Application;

class DebugClassProvider extends Command
{
    protected function configure(): void
    {
        $this->setName('debug:class-provider')
            ->setDescription('Debug class provider')
            ->addArgument('class-name', InputArgument::OPTIONAL, 'The name of the class to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $className = $input->getArgument('class-name');
        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new \RuntimeException('Application is not an instance of Application');
        }

        if ($className !== null) {
            if (!is_string($className)) {
                throw new \InvalidArgumentException('Class name must be a string');
            }

            $class = $application->getClassFinder()->find($className);
            if ($class === null) {
                throw new \InvalidArgumentException(sprintf('Class %s not found', $className));
            }
            $output->writeln(sprintf('Debugging class %s', $class->fullyQualifiedIdentifier()));
            return Command::SUCCESS;
        }

        $output->writeln('Debug class provider');
        $classes = $application->getClassProvider()->provide();
        foreach ($classes->toArray() as $class) {
            $output->writeln(sprintf('%s (%s)', $class->fullyQualifiedIdentifier(), $class->namespace()->name()));
        }

        return Command::SUCCESS;
    }
}
