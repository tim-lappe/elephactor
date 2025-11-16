<?php

declare (strict_types=1);

namespace TimLappe\Elephactor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\MoveFile;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Adapter\Workspace\FsDirectory;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

use function sprintf;

class MoveClass extends Command
{
    protected function configure(): void
    {
        $this->setName('class:move')
            ->setDescription('Move a class')
            ->addArgument('class-name', InputArgument::REQUIRED, 'The name of the class to move')
            ->addArgument('new-directory', InputArgument::REQUIRED, 'The new directory of the class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $className = $input->getArgument('class-name');
        $newDirectory = $input->getArgument('new-directory');

        if (!is_string($className) || !is_string($newDirectory)) {
            throw new \InvalidArgumentException('Class name and new directory must be strings');
        }

        $className = trim($className);
        $className = trim($className, '\\');
        $className = QualifiedName::fromString($className);

        $newDirectory = trim($newDirectory);
        $output->writeln(sprintf('Moving class %s to directory %s', $className, $newDirectory));

        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new \RuntimeException('Application is not an instance of Application');
        }

        $class = $application->workspace()->classLikeIndex()->find(new ClassNameCriteria($className))->first();
        if (!$class instanceof Psr4ClassFile) {
            throw new \RuntimeException(sprintf('Class %s not found in workspace', $className));
        }

        $pwd = getcwd();
        if ($pwd !== false && !str_starts_with($newDirectory, '/')) {
            $newDirectory = $pwd . '/' . trim($newDirectory, '/');
        }

        $newDirectory = realpath($newDirectory);
        if ($newDirectory === false) {
            throw new \RuntimeException(sprintf('Directory %s does not exist', $newDirectory));
        }

        $fsDirectory = new FsDirectory($newDirectory);

        $refactoringExecutor = $application->refactoringExecutor();
        $refactoringExecutor->handle(new MoveFile($class->file(), $fsDirectory));

        return Command::SUCCESS;
    }
}
