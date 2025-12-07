<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\VisitorContext;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\ConvertToFullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;

final class ConvertImplicitToFullyQualifiedNameTransformer extends AbsractNodeTransformer
{
    public function enter(Node $node, VisitorContext $context): void
    {
        if ($node instanceof NamespaceDefinitionNode) {
            $context->set('namespaceNode', $node);
        }

        if ($node instanceof UseStatementNode) {
            $context->set('insideUseStatement', true);

            $identifiers = $node->identifiersImported();
            $identifiersImported = $context->getArray('identifiersImported');
            $context->set('identifiersImported', [...$identifiersImported, ...$identifiers]);
        }

        if ($node instanceof QualifiedNameNode) {
            if ($context->getBoolean('insideUseStatement') === true) {
                return;
            }

            if (!$context->has('namespaceNode')) {
                return;
            }

            if ($node->qualifiedName() instanceof FullyQualifiedName) {
                return;
            }

            $namespaceNode = $context->get('namespaceNode', NamespaceDefinitionNode::class);
            if ($namespaceNode->name() === $node) {
                return;
            }

            $identifiersImported = $context->getArray('identifiersImported');
            if (in_array($node->qualifiedName()->lastPart(), $identifiersImported, true)) {
                return;
            }

            if ($node->qualifiedName()->isReservedTypeName()) {
                return;
            }

            $this->refactorings->add(new ConvertToFullyQualifiedName($namespaceNode, $node));
        }
    }

    public function leave(Node $node, VisitorContext $context): void
    {
        if ($node instanceof UseStatementNode && $context->has('insideUseStatement')) {
            $context->set('insideUseStatement', false);
        }
    }
}
