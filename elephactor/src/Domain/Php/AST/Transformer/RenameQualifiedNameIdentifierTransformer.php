<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\VisitorContext;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\IdentifierChanger;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\QualifiedNameChanger;

final class RenameQualifiedNameIdentifierTransformer extends AbsractNodeTransformer
{
    public function __construct(
        private readonly FullyQualifiedName $fullName,
        private readonly Identifier $newIdentifier,
        private readonly ?FullyQualifiedName $replacementFullyQualifiedName = null,
    ) {
        parent::__construct();
    }

    public function enter(Node $node, VisitorContext $context): void
    {
        if ($node instanceof UseStatementNode) {
            $context->set('insideUseStatement', true);
            return;
        }

        if ($context->has('insideUseStatement') && $context->getBoolean('insideUseStatement') === true) {
            return;
        }

        if ($node instanceof QualifiedNameNode) {
            $oldIdentifier = $this->fullName->lastPart();
            if ($node->qualifiedName()->equals($this->fullName)) {
                $newQualifiedName = $this->replacementFullyQualifiedName ?? $node->qualifiedName()->changeLastPart($this->newIdentifier);
                $this->refactorings->add(new QualifiedNameChanger($node, $newQualifiedName));
                return;
            }

            if ($node->qualifiedName()->lastPart()->equals($oldIdentifier)) {
                $newQualifiedName = $node->qualifiedName()->changeLastPart($this->newIdentifier);
                $this->refactorings->add(new QualifiedNameChanger($node, $newQualifiedName));
            }
        }

        if ($node instanceof IdentifierNode) {
            if ($node->identifier()->equals($this->fullName->lastPart())) {
                $this->refactorings->add(new IdentifierChanger($node, $this->newIdentifier));
            }
        }
    }

    public function leave(Node $node, VisitorContext $context): void
    {
        if ($node instanceof UseStatementNode && $context->has('insideUseStatement')) {
            $context->set('insideUseStatement', false);
        }
    }
}
