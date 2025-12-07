<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring\Executors;

use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringExecutor;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;
use TimLappe\Elephactor\Domain\Workspace\Model\Workspace;
use TimLappe\Elephactor\Domain\Php\AST\Analysis\FqnResolver;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\NodeTransformationExecutor;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\RenameImportTransformer;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\RenameQualifiedNameIdentifierTransformer;
use TimLappe\Elephactor\Domain\Php\Refactoring\FileRefactoringReport;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringReport;

final class ClassRenameExecutor implements RefactoringExecutor
{
    public function __construct(
        private readonly PhpFilePersister $phpFilePersister,
        private readonly Workspace $workspace,
    ) {
    }

    public function supports(RefactoringCommand $command): bool
    {
        return $command instanceof ClassRename;
    }

    public function handle(RefactoringCommand $command, bool $dryRun = false): RefactoringReport
    {
        $report = new RefactoringReport();

        if (!$command instanceof ClassRename) {
            throw new \InvalidArgumentException('Command is not a ClassRename');
        }

        $phpFiles = $this->workspace->phpFileIndex()->find()->toArray();
        $fqnResolver = new FqnResolver($command->phpClass()->file()->fileNode());
        $oldFullyQualifiedName = $fqnResolver->resolve($command->phpClass()->classLikeNode()->name()->identifier());
        $newFullyQualifiedName = $oldFullyQualifiedName->changeLastPart($command->newName());

        $dirtyPhpFiles = [];
        foreach ($phpFiles as $phpFile) {
            $nodeTransformationExecutor = new NodeTransformationExecutor();
            $nodeTransformationExecutor->addTransformer(new RenameQualifiedNameIdentifierTransformer($oldFullyQualifiedName, $command->newName()));
            $nodeTransformationExecutor->addTransformer(new RenameImportTransformer($oldFullyQualifiedName, $newFullyQualifiedName));

            $refactoringResult = $nodeTransformationExecutor->apply($phpFile->fileNode());
            if (count($refactoringResult->appliedRefactorings()) > 0) {
                $dirtyPhpFiles[] = $phpFile;
                $report->addFileRefactoringReport(new FileRefactoringReport($phpFile, $refactoringResult));
            }
        }

        foreach ($dirtyPhpFiles as $phpFile) {
            if (!$dryRun) {
                $this->phpFilePersister->persist($phpFile);
            }
        }

        if (!$dryRun) {
            $command->phpClass()->file()->handle()->rename($command->newName()->value() . '.php');
            $this->phpFilePersister->persist($command->phpClass()->file());
        }

        return $report;
    }
}
