<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticUseStatementCollection;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\ConvertToFullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;

final class ConvertImplicitToFullyQualifiedNameTransformer extends AbstractSemanticNodeTransformer
{
    public function __construct(
        private readonly NamespacedScope $namespaceScope,
        private readonly SemanticUseStatementCollection $importList,
    ) {
    }

    public function enter(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
        if ($semanticNode instanceof SemanticQualifiedNameNode) {
            $alreadyImportedFullyQualifiedNames = $this->importList->fullyQualifiedNames();
            $fullyQualifiedName = $this->namespaceScope->resolveQualifiedName($semanticNode->qualifiedName());

            if ($alreadyImportedFullyQualifiedNames->contains($fullyQualifiedName)) {
                return;
            }

            if ($semanticNode->nameKind() === NameKind::NamespaceDecleration || $semanticNode->nameKind() === NameKind::UseStatement) {
                return;
            }

            $refactoringStack->add(new ConvertToFullyQualifiedName($this->namespaceScope, $semanticNode->qualifiedNameNode()));
        }
    }

    public function leave(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
    }
}