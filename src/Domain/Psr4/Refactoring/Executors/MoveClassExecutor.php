<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Refactoring\Executors;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AliasMap;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\FileNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringCommand;
use TimLappe\Elephactor\Domain\Php\Refactoring\RefactoringExecutor;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;
use TimLappe\Elephactor\Domain\Php\Resolution\ClassReference\ClassReference;
use TimLappe\Elephactor\Domain\Php\Resolution\ClassReference\ClassReferenceFinder;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Psr4\Refactoring\Commands\MoveClassFile;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4FileIndex;

final class MoveClassExecutor implements RefactoringExecutor
{
    public function __construct(
        private readonly PhpFilePersister $phpFilePersister,
        private readonly Psr4FileIndex $fileIndex,
        private readonly ClassReferenceFinder $classReferenceFinder,
    ) {
    }

    public function supports(RefactoringCommand $command): bool
    {
        return $command instanceof MoveClassFile;
    }

    public function handle(RefactoringCommand $command): void
    {
        if (!$command instanceof MoveClassFile) {
            throw new \InvalidArgumentException('Command is not a MoveClassFile');
        }

        $classFile = $command->psr4ClassFile();
        $references = $this->classReferenceFinder->findClassReferences($classFile);
        $oldFullyQualifiedName = $classFile->fullyQualifiedIdentifier();

        $newNamespace = $this->fileIndex->autoloadMap()->resolveNamespaceForDirectory($command->newDirectory());
        if ($newNamespace === null) {
            throw new \RuntimeException(sprintf('Namespace for directory %s not found', $command->newDirectory()->name()));
        }

        $newNamespaceName = $this->buildQualifiedNameFromNamespace($newNamespace);
        $newFullyQualifiedName = $this->buildFullyQualifiedName($newNamespace, $classFile);

        $classFile->file()->handle()->moveTo($command->newDirectory());
        $this->updateNamespaceStatement($classFile->file()->fileNode(), $newNamespaceName);
        $this->phpFilePersister->persist($classFile->file());

        $this->updateReferences($references, $oldFullyQualifiedName, $newFullyQualifiedName);
    }


    private function buildQualifiedNameFromNamespace(PhpNamespace $namespace): QualifiedName
    {
        return new QualifiedName($this->cloneIdentifiers($namespace->parts()));
    }

    private function buildFullyQualifiedName(PhpNamespace $namespace, Psr4ClassFile $classFile): FullyQualifiedName
    {
        return new FullyQualifiedName([
            ...$this->cloneIdentifiers($namespace->parts()),
            new Identifier($classFile->identifier()->value()),
        ]);
    }

    /**
     * @param list<ClassReference> $references
     */
    private function updateReferences(array $references, FullyQualifiedName $oldFullyQualifiedName, FullyQualifiedName $newFullyQualifiedName): void
    {
        foreach ($references as $reference) {
            $file = $reference->file();
            $fileNode = $file->fileNode();
            $aliasMap = $this->buildAliasMap($fileNode);
            $useClauseNodes = $this->collectUseClauseNodes($fileNode);
            $useClauseOverrides = [];

            foreach ($reference->referenceNodes() as $referenceNode) {
                if (isset($useClauseNodes[spl_object_id($referenceNode)])) {
                    $useClauseOverrides[spl_object_id($referenceNode)] = $this->cloneIdentifiers($newFullyQualifiedName->parts());
                    continue;
                }

                if ($this->shouldSkipReferenceReplacement($referenceNode, $aliasMap, $oldFullyQualifiedName)) {
                    continue;
                }

                $referenceNode->replaceParts($this->cloneIdentifiers($newFullyQualifiedName->parts()));
            }

            $this->normalizeUseStatements($file, $useClauseOverrides);
            $this->phpFilePersister->persist($file);
        }
    }

    private function updateNamespaceStatement(FileNode $fileNode, QualifiedName $newNamespace): void
    {
        foreach ($fileNode->statements() as $statement) {
            if ($statement instanceof NamespaceDefinitionNode) {
                $statement->changeName($newNamespace);

                return;
            }
        }

        throw new \RuntimeException('Namespace statement not found in file');
    }

    /**
     * @return array<int, true>
     */
    private function collectUseClauseNodes(FileNode $fileNode): array
    {
        $map = [];
        foreach ($fileNode->useStatements()->toArray() as $useStatement) {
            foreach ($useStatement->clauses() as $clause) {
                $map[spl_object_id($clause->name())] = true;
            }
        }

        return $map;
    }

    private function shouldSkipReferenceReplacement(QualifiedName $referenceNode, AliasMap $aliasMap, FullyQualifiedName $oldFullyQualifiedName): bool
    {
        $parts = $referenceNode->parts();
        if (count($parts) !== 1) {
            return false;
        }

        $aliasIdentifier = $parts[0];
        if (!$aliasMap->has($aliasIdentifier)) {
            return false;
        }

        $aliasTarget = $aliasMap->get($aliasIdentifier);
        $resolvedAlias = new FullyQualifiedName($this->cloneIdentifiers($aliasTarget->parts()));

        return $resolvedAlias->equals($oldFullyQualifiedName);
    }

    /**
     * @param array<int, list<Identifier>> $useClauseOverrides
     */
    private function normalizeUseStatements(PhpFile $file, array $useClauseOverrides = []): void
    {
        foreach ($file->fileNode()->useStatements()->toArray() as $useStatement) {
            $this->normalizeUseStatement($useStatement, $useClauseOverrides);
        }
    }

    /**
     * @param array<int, list<Identifier>> $useClauseOverrides
     */
    private function normalizeUseStatement(UseStatementNode $useStatement, array $useClauseOverrides): void
    {
        if ($useStatement->groupPrefix() === null) {
            foreach ($useStatement->clauses() as $clause) {
                $clauseId = spl_object_id($clause->name());
                if (isset($useClauseOverrides[$clauseId])) {
                    $clause->name()->replaceParts($this->cloneIdentifiers($useClauseOverrides[$clauseId]));
                }
            }

            return;
        }

        $absoluteClauses = [];
        foreach ($useStatement->clauses() as $clause) {
            $clauseId = spl_object_id($clause->name());
            if (isset($useClauseOverrides[$clauseId])) {
                $absoluteClauses[] = $useClauseOverrides[$clauseId];
                continue;
            }

            $absoluteClauses[] = $this->resolveUseClauseParts($useStatement, $clause->name());
        }

        $commonPrefix = $this->findCommonPrefix($absoluteClauses);
        if ($commonPrefix === []) {
            $useStatement->changeGroupPrefix(null);
            foreach ($useStatement->clauses() as $index => $clause) {
                $clause->name()->replaceParts($this->cloneIdentifiers($absoluteClauses[$index]));
            }

            return;
        }

        $useStatement->changeGroupPrefix(new QualifiedName($this->cloneIdentifiers($commonPrefix)));
        foreach ($useStatement->clauses() as $index => $clause) {
            $relativeParts = array_slice($absoluteClauses[$index], count($commonPrefix));
            if ($relativeParts === []) {
                $relativeParts = [end($commonPrefix)];
            }

            $clause->name()->replaceParts($this->cloneIdentifiers($relativeParts));
        }
    }

    /**
     * @return list<Identifier>
     */
    private function resolveUseClauseParts(UseStatementNode $useStatement, QualifiedName $clauseName): array
    {
        $parts = $clauseName->parts();
        $groupPrefix = $useStatement->groupPrefix();

        if ($groupPrefix === null) {
            return $parts;
        }

        return [...$groupPrefix->parts(), ...$parts];
    }

    private function buildAliasMap(FileNode $fileNode): AliasMap
    {
        $aliasMap = new AliasMap();
        foreach ($fileNode->useStatements()->filterKind(UseKind::CLASS_IMPORT)->toArray() as $useStatement) {
            $aliasMap->merge($this->extractAliasesFromUseStatement($useStatement));
        }

        return $aliasMap;
    }

    private function extractAliasesFromUseStatement(UseStatementNode $useStatement): AliasMap
    {
        $aliasMap = new AliasMap();
        foreach ($useStatement->clauses() as $clause) {
            $alias = $clause->alias() ?? $clause->name()->lastPart();
            $parts = $this->resolveUseClauseParts($useStatement, $clause->name());
            $aliasMap->add($alias, new QualifiedName($this->cloneIdentifiers($parts), true));
        }

        return $aliasMap;
    }

    /**
     * @param  list<list<Identifier>> $lists
     * @return list<Identifier>
     */
    private function findCommonPrefix(array $lists): array
    {
        if ($lists === []) {
            return [];
        }

        $prefix = $lists[0];

        foreach ($lists as $list) {
            $max = min(count($prefix), count($list));
            $newPrefix = [];

            for ($i = 0; $i < $max; $i++) {
                if (!$prefix[$i]->equals($list[$i])) {
                    break;
                }

                $newPrefix[] = $prefix[$i];
            }

            $prefix = $newPrefix;

            if ($prefix === []) {
                break;
            }
        }

        return $prefix;
    }

    /**
     * @param  list<Identifier> $identifiers
     * @return list<Identifier>
     */
    private function cloneIdentifiers(array $identifiers): array
    {
        return array_map(
            static fn (Identifier $identifier): Identifier => new Identifier($identifier->value()),
            $identifiers,
        );
    }
}
