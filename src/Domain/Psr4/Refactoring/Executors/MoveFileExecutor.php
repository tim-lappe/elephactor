<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Refactoring\Executors;

use TimLappe\Elephactor\Domain\Php\AST\Transformer\RenameImportTransformer;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\NodeTransformationExecutor;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\MoveFile;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringExecutor;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4PhpFileIndex;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\RenameNamespaceDeclerationTransformer;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\ConvertImplicitToFullyQualifiedNameTransformer;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\RenameQualifiedNameIdentifierTransformer;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\Refactoring\FileRefactoringReport;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringReport;

final class MoveFileExecutor implements RefactoringExecutor
{
    public function __construct(
        private readonly Psr4PhpFileIndex $fileIndex,
        private readonly PhpFilePersister $phpFilePersister,
    ) {
    }

    public function supports(RefactoringCommand $command): bool
    {
        return $command instanceof MoveFile && count($command->phpFile()->fileNode()->classLikeDeclerations()) === 1;
    }

    public function handle(RefactoringCommand $command, bool $dryRun = false): RefactoringReport
    {
        $report = new RefactoringReport();

        if (!$command instanceof MoveFile) {
            throw new \InvalidArgumentException('Command is not a MoveFile');
        }

        $classFile = new Psr4ClassFile($command->phpFile());
        $oldFullyQualifiedName = $classFile->fullyQualifiedName();
        $oldNamespaceQualifiedName = $oldFullyQualifiedName->removeLastPart();

        $phpFiles = $this->fileIndex->find()->toArray();
        $newNamespace = $this->fileIndex->resolveNamespaceForDirectory($command->newDirectory());
        if ($newNamespace === null) {
            throw new \RuntimeException(sprintf('Namespace for directory %s not found', $command->newDirectory()->name()));
        }

        $newFullyQualifiedName = $newNamespace->extend($oldFullyQualifiedName->lastPart());
        $newFullyQualifiedNameFq = new FullyQualifiedName($newFullyQualifiedName->parts());

        $dirtyPhpFiles = [];
        foreach ($phpFiles as $phpFile) {
            $semanticFileNode = $phpFile->fileNode();
            $semanticNodeTraverser = new NodeTransformationExecutor();
            $semanticNodeTraverser->addTransformer(new RenameImportTransformer($oldFullyQualifiedName, $newFullyQualifiedName));
            $semanticNodeTraverser->addTransformer(new RenameQualifiedNameIdentifierTransformer($oldFullyQualifiedName, $newFullyQualifiedName->lastPart(), $newFullyQualifiedNameFq));
            $refactoringResult = $semanticNodeTraverser->apply($semanticFileNode);

            if (count($refactoringResult->appliedRefactorings()) > 0) {
                $dirtyPhpFiles[] = $phpFile;
                $report->addFileRefactoringReport(new FileRefactoringReport($phpFile, $refactoringResult));
            }
        }

        $classFileNode = $classFile->file()->fileNode();
        foreach ($dirtyPhpFiles as $phpFile) {
            if (!$dryRun) {
                $this->phpFilePersister->persist($phpFile);
            }
        }

        $preNamespaceTransformations = new NodeTransformationExecutor();
        $preNamespaceTransformations->addTransformer(new ConvertImplicitToFullyQualifiedNameTransformer());
        $preNamespaceTransformations->apply($classFileNode);

        $semanticNodeTraverser = new NodeTransformationExecutor();
        $semanticNodeTraverser->addTransformer(new RenameNamespaceDeclerationTransformer($oldNamespaceQualifiedName, $newNamespace));
        $semanticNodeTraverser->addTransformer(new RenameImportTransformer($oldFullyQualifiedName, $newFullyQualifiedName));
        $semanticNodeTraverser->addTransformer(new RenameQualifiedNameIdentifierTransformer($oldFullyQualifiedName, $newFullyQualifiedName->lastPart(), $newFullyQualifiedNameFq));
        $refactoringResult = $semanticNodeTraverser->apply($classFileNode);
        if (count($refactoringResult->appliedRefactorings()) > 0) {
            $report->addFileRefactoringReport(new FileRefactoringReport($classFile->file(), $refactoringResult));
        }

        if (!$dryRun) {
            $classFile->file()->handle()->moveTo($command->newDirectory());
            $this->phpFilePersister->persist($classFile->file());
        }

        return $report;
    }
}
