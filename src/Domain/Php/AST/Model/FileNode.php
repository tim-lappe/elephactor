<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNodeCollection;

final class FileNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly array $statements
    ) {
        parent::__construct(NodeKind::FILE);
    }

    public function useStatements(): UseStatementNodeCollection
    {
        return new UseStatementNodeCollection($this->findNestedChildrenOfType(UseStatementNode::class));
    }

    /**
     * @return list<NamespaceDefinitionNode>
     */
    public function namespaceDefinitions(): array
    {
        return $this->findNestedChildrenOfType(NamespaceDefinitionNode::class);
    }

    public function currentNamespace(): ?QualifiedNameNode
    {
        $namespaceDeclaration = $this->findNestedChildrenOfType(NamespaceDefinitionNode::class);
        if ($namespaceDeclaration === []) {
            return null;
        }

        $name = $namespaceDeclaration[0]->name();
        return $name;
    }

    /**
     * @return ?ClassLikeNode
     */
    public function findClassLikeDeclaration(string $name): ClassLikeNode|null
    {
        $declarations = $this->findNestedChildrenOfType(ClassLikeNode::class);
        foreach ($declarations as $declaration) {
            if ($declaration->name()->equals($name)) {
                return $declaration;
            }
        }

        return null;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->statements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->statements;
    }
}
