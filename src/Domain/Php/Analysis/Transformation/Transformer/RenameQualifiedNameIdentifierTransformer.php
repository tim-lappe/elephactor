<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticIdentifierNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\IdentifierChanger;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\QualifiedNameChanger;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class RenameQualifiedNameIdentifierTransformer extends AbstractSemanticNodeTransformer
{
    public function __construct(
        private readonly FullyQualifiedName $fullName,
        private readonly Identifier $newIdentifier,
    ) {
    }

    public function enter(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
        if ($semanticNode instanceof SemanticQualifiedNameNode) {
            if ($semanticNode->fullyQualifiedName()->equals($this->fullName)) {
                $newQualifiedName = $semanticNode->qualifiedName()->changeLastPart($this->newIdentifier);
                $refactoringStack->add(new QualifiedNameChanger($semanticNode->qualifiedNameNode(), $newQualifiedName));
            }
        }

        if ($semanticNode instanceof SemanticIdentifierNode) {
            if ($semanticNode->fullyQualifiedName()->equals($this->fullName)) {
                $refactoringStack->add(new IdentifierChanger($semanticNode->identifierNode(), $this->newIdentifier));
            }
        }
    }

    public function leave(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {

    }
}
