<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\QualifiedNameChanger;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class RenameNamespaceDeclerationTransformer extends AbstractSemanticNodeTransformer
{
    public function __construct(
        private readonly QualifiedName $oldName,
        private readonly QualifiedName $newName,
    ) {
    }

    public function enter(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
        if ($semanticNode instanceof SemanticFileNode) {
            $namespaceDefinitions = $semanticNode->fileNode()->namespaceDefinitions();
            foreach ($namespaceDefinitions as $namespaceDefinition) {
                if ($namespaceDefinition->name()->qualifiedName()->equals($this->oldName)) {
                    $downgradedName = new QualifiedName($this->newName->parts());
                    $refactoringStack->add(new QualifiedNameChanger($namespaceDefinition->name(), $downgradedName));
                }
            }
        }
    }

    public function leave(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
    }
}