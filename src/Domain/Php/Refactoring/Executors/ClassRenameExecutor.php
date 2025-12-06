<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring\Executors;

use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer\RenameQualifiedNameIdentifierTransformer;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer\RenameImportTransformer;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringExecutor;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;
use TimLappe\Elephactor\Domain\Workspace\Model\Workspace;
use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\SemanticNodeTransformationExecutor;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\AST\Analysis\FqnResolver;

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

    public function handle(RefactoringCommand $command): void
    {
        if (!$command instanceof ClassRename) {
            throw new \InvalidArgumentException('Command is not a ClassRename');
        }

        $phpFiles = $this->workspace->phpFileIndex()->find()->toArray();

        $fqnResolver = new FqnResolver($command->phpClass()->file()->fileNode(), $command->phpClass()->classLikeNode()->name()->identifier());
        $oldFullyQualifiedName = $fqnResolver->fullyQualifiedName();
        if ($oldFullyQualifiedName === null) {
            throw new \RuntimeException('Could not resolve fully qualified name for class ' . $command->phpClass()->classLikeNode()->name()->identifier()->value());
        }

        $newFullyQualifiedName = $oldFullyQualifiedName->changeLastPart($command->newName());

        $refactoringStack = new RefactoringStack();
        foreach ($phpFiles as $phpFile) {
            $semanticFileNode = $phpFile->fileNode();
            $semanticNodeTraverser = new SemanticNodeTransformationExecutor([
                new RenameQualifiedNameIdentifierTransformer($oldFullyQualifiedName, $command->newName()),
                new RenameImportTransformer($oldFullyQualifiedName, $newFullyQualifiedName),
            ]);
            
            $semanticNodeTraverser->collect($semanticFileNode, $refactoringStack);
        }

        $refactoringStack->apply();

        foreach ($phpFiles as $phpFile) {
            $this->phpFilePersister->persist($phpFile);
        }

        $this->renameItself($command->phpClass(), $command->newName());
    }

    private function renameItself(PhpClassLike $classLike, Identifier $newName): void
    {
        $classLike->file()->handle()->rename($newName->value() . '.php');

        $this->phpFilePersister->persist($classLike->file());
    }
}
