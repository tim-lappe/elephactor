<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Analysis;

use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Visitor\NodeVisitor;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\NodeTraverser;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

final class FqnResolver implements NodeVisitor
{
    private ?FullyQualifiedName $fullyQualifiedName = null;
    private ?NamespaceDefinitionNode $currentNamespaceDefinitionNode = null;

    public function __construct(
        FileNode $fileNode,
        private readonly Identifier $identifier,
    ) {
        $traverser = new NodeTraverser([$this]);
        $traverser->traverse($fileNode);
    }

    public function fullyQualifiedName(): ?FullyQualifiedName
    {
        return $this->fullyQualifiedName;
    }

    public function enter(Node $node): void
    {
        if ($this->fullyQualifiedName !== null) {
            return;
        }

        if ($node instanceof NamespaceDefinitionNode) {
            $this->currentNamespaceDefinitionNode = $node;
        }

        if ($node instanceof IdentifierNode) {
            if ($node->identifier()->equals($this->identifier)) {
                if ($this->currentNamespaceDefinitionNode !== null) {
                    $this->fullyQualifiedName = new FullyQualifiedName($this->currentNamespaceDefinitionNode->name()->qualifiedName()->extend($node->identifier())->parts());
                }

                if ($this->currentNamespaceDefinitionNode === null) {
                    $this->fullyQualifiedName = new FullyQualifiedName([$node->identifier()]);
                }
            }
        }
    }

    public function leave(Node $node): void
    {
    }
}