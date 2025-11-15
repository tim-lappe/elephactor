<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Command\Debug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Domain\Psr4\Repository\Psr4RootsLoader;
use TimLappe\Elephactor\Debug\NamespaceSegmentPrinter;

class DebugNamespaceTree extends Command
{
    public function __construct(
        private readonly Psr4RootsLoader $psr4RootsLoader,
    ) {
        parent::__construct('debug:namespace-tree');
    }

    protected function configure(): void
    {
        $this->setName('debug:namespace-tree')
            ->setDescription('Debug namespace tree');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Debug namespace tree');

        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw new \RuntimeException('Application is not an instance of Application');
        }

        $psr4Roots = $this->psr4RootsLoader->load();
        foreach ($psr4Roots->roots() as $psr4Root) {
            $printer = new NamespaceSegmentPrinter($output);
            $printer->printTree($psr4Root->rootSegment());
        }

        return Command::SUCCESS;
    }
}
