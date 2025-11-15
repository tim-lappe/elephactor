<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Debug;

use Symfony\Component\Console\Output\OutputInterface;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4NamespaceSegment;
use TimLappe\Elephactor\Adapter\Filesystem\NativeDirectoryHandle;

final class NamespaceSegmentPrinter
{
    private const TREE_BRANCH = '├── ';
    private const TREE_CORNER = '└── ';
    private const TREE_VERTICAL = '│   ';
    private const TREE_SPACE = '    ';

    public function __construct(
        private readonly OutputInterface $output,
    ) {
    }

    public function printTree(Psr4NamespaceSegment $rootSegment): void
    {
        $this->output->writeln('<info>Namespace Segment Tree:</info>');
        $this->output->writeln('');
        $this->printSegment($rootSegment, '', true);
    }

    private function printSegment(Psr4NamespaceSegment $segment, string $prefix, bool $isLast): void
    {
        $treeSymbol = $isLast ? self::TREE_CORNER : self::TREE_BRANCH;
        $segmentName = $segment->identifier()->name();

        if ($segment->directoryHandle() !== null) {
            $directoryHandle = $segment->directoryHandle();
            if (!$directoryHandle instanceof NativeDirectoryHandle) {
                throw new \RuntimeException('Directory handle must be a NativeDirectoryHandle');
            }

            $segmentName = sprintf(
                '<comment>%s</comment> %s',
                $segment->identifier()->name(),
                $directoryHandle->absolutePath()
            );
        } else {
            $segmentName = sprintf('%s', $segment->identifier()->name());
        }

        $this->output->writeln($prefix . $treeSymbol . $segmentName);

        $this->printClasses($segment, $prefix, $isLast);

        $childSegments = $segment->childSegments();
        $childCount = count($childSegments);

        foreach ($childSegments as $index => $childSegment) {
            $isLastChild = ($index === $childCount - 1);
            $childPrefix = $prefix . ($isLast ? self::TREE_SPACE : self::TREE_VERTICAL);
            $this->printSegment($childSegment, $childPrefix, $isLastChild);
        }
    }

    private function printClasses(Psr4NamespaceSegment $segment, string $prefix, bool $isLast): void
    {
        $classes = $segment->classes()->toArray();

        if (count($classes) === 0) {
            return;
        }

        $classPrefix = $prefix . ($isLast ? self::TREE_SPACE : self::TREE_VERTICAL);

        foreach ($classes as $class) {
            $className = '(C) ' . $class->identifier()->value();
            $this->output->writeln(sprintf('%s<comment>%s</comment>', $classPrefix . '├── ', $className));
        }
    }
}
