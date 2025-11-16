<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Refactoring\Executors;

use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer\RenameImportTransformer;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\SemanticNodeTransformationExecutor;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\MoveFile;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringExecutor;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4PhpFileIndex;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer\RenameNamespaceDeclerationTransformer;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer\ConvertImplicitToFullyQualifiedNameTransformer;

final class MoveFileExecutor implements RefactoringExecutor
{
    public function __construct(
        private readonly Psr4PhpFileIndex $fileIndex,
        private readonly PhpFilePersister $phpFilePersister,
    ) {
    }

    public function supports(RefactoringCommand $command): bool
    {
        return $command instanceof MoveFile && count($command->phpFile()->fileNode()->classLikeDeclarations()) === 1;
    }

    public function handle(RefactoringCommand $command): void
    {
        if (!$command instanceof MoveFile) {
            throw new \InvalidArgumentException('Command is not a MoveFile');
        }

        $classFile = new Psr4ClassFile($command->phpFile());
        $oldFullyQualifiedName = $classFile->classLikeDeclaration()->name()->fullyQualifiedName();
        $oldNamespaceQualifiedName = $oldFullyQualifiedName->removeLastPart();

        $phpFiles = $this->fileIndex->find()->toArray();
        $newNamespace = $this->fileIndex->resolveNamespaceForDirectory($command->newDirectory());
        $newNamespaceQualifiedName = $newNamespace?->name();
        if ($newNamespaceQualifiedName === null) {
            throw new \RuntimeException(sprintf('Namespace for directory %s not found', $command->newDirectory()->name()));
        }

        $newFullyQualifiedName = $newNamespace->fullyQualifyName($oldFullyQualifiedName->lastPart());
        $refactoringStack = new RefactoringStack();

        foreach ($phpFiles as $phpFile) {
            $semanticFileNode = $phpFile->fileNode();
            $semanticNodeTraverser = new SemanticNodeTransformationExecutor([
                new RenameImportTransformer($oldFullyQualifiedName, $newFullyQualifiedName),
            ]);

            $semanticNodeTraverser->collect($semanticFileNode, $refactoringStack);
            $this->phpFilePersister->persist($phpFile);
        }

        $semanticNodeTransformationExecutor = new SemanticNodeTransformationExecutor([
            new RenameNamespaceDeclerationTransformer($oldNamespaceQualifiedName, $newNamespaceQualifiedName),
            new RenameImportTransformer($oldFullyQualifiedName, $newFullyQualifiedName),
            new ConvertImplicitToFullyQualifiedNameTransformer($classFile->file()->fileNode()->namespaceScope(), $classFile->file()->fileNode()->imports()),
        ]);

        $semanticNodeTransformationExecutor->collect($classFile->file()->fileNode(), $refactoringStack);
        $refactoringStack->apply();

        $classFile->file()->handle()->moveTo($command->newDirectory());
        $this->phpFilePersister->persist($classFile->file());
    }
}
