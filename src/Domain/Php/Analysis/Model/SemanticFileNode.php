<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticClassLikeDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticUseStatementCollection;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\FileScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;

final class SemanticFileNode extends AbstractSemanticNode
{
    /**
     * @var list<SemanticClassLikeDecleration> $classLikeDeclarations
     */
    private array $classLikeDeclarations = [];

    private SemanticUseStatementCollection $imports;

    public function __construct(
        private FileNode $fileNode,
    ) {
        $this->imports = new SemanticUseStatementCollection(new NamespacedScope(
            $this->fileScope(),
            $this->namespace()
        ), $this->fileNode()->useStatements());
    }

    public function fileScope(): FileScope
    {
        return new FileScope($this);
    }

    public function namespaceScope(): NamespacedScope
    {
        return new NamespacedScope($this->fileScope(), $this->namespace());
    }

    public function namespace(): PhpNamespace
    {
        $namespace = $this->fileNode()->currentNamespace();
        if ($namespace === null) {
            return new PhpNamespace();
        }

        return new PhpNamespace($namespace->qualifiedName());
    }

    public function namespaceQualifiedNameNode(): ?SemanticQualifiedNameNode
    {
        $namespace = $this->fileNode()->currentNamespace();
        if ($namespace === null) {
            return null;
        }

        return new SemanticQualifiedNameNode($this->namespaceScope(), $namespace, NameKind::NamespaceDecleration);
    }

    public function addClassLikeDecleration(SemanticClassLikeDecleration $classLikeDeclaration): void
    {
        $this->classLikeDeclarations[] = $classLikeDeclaration;
    }

    /**
     * @return list<SemanticClassLikeDecleration>
     */
    public function classLikeDeclarations(): array
    {
        return $this->classLikeDeclarations;
    }

    public function imports(): SemanticUseStatementCollection
    {
        return $this->imports;
    }

    public function fileNode(): FileNode
    {
        return $this->fileNode;
    }

    public function children(): array
    {
        return array_values(array_filter(
            [...parent::children(), $this->namespaceQualifiedNameNode(), $this->imports, ...$this->classLikeDeclarations],
            fn ($child) => $child !== null
        ));
    }

    public function __toString(): string
    {
        return 'File';
    }
}
