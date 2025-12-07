<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Analysis;

use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\NodeVisitor;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\NodeTraverser;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\VisitorContext;

final class FqnResolver implements NodeVisitor
{
    public function __construct(
        private readonly FileNode $fileNode,
    ) {
    }

    public function resolve(Identifier $identifier): FullyQualifiedName
    {
        $traverser = new NodeTraverser([$this]);

        $context = new VisitorContext();
        $context->set('identifier', $identifier);

        $traverser->traverse($this->fileNode, $context);

        return $context->get('fullyQualifiedName', FullyQualifiedName::class);
    }

    public function enter(Node $node, VisitorContext $context): void
    {
        if ($context->has('fullyQualifiedName')) {
            return;
        }

        if ($node instanceof NamespaceDefinitionNode) {
            $context->set('currentNamespaceDefinitionNode', $node);
        }

        if ($node instanceof IdentifierNode) {
            if ($node->identifier()->equals($context->get('identifier', Identifier::class))) {
                if ($context->has('currentNamespaceDefinitionNode')) {
                    $context->set('fullyQualifiedName', new FullyQualifiedName($context->get('currentNamespaceDefinitionNode', NamespaceDefinitionNode::class)->name()->qualifiedName()->extend($node->identifier())->parts()));
                    return;
                }

                $context->set('fullyQualifiedName', new FullyQualifiedName([$node->identifier()]));
                return;
            }
        }
    }

    public function leave(Node $node, VisitorContext $context): void
    {
    }
}
