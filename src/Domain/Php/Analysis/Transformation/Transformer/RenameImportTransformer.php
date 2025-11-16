<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Transformer;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticUseStatementCollection;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\QualifiedNameChanger;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class RenameImportTransformer extends AbstractSemanticNodeTransformer
{
    public function __construct(
        private readonly FullyQualifiedName $oldFullyQualifiedName,
        private readonly FullyQualifiedName $newFullyQualifiedName,
    ) {
    }

    public function enter(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
        if ($semanticNode instanceof SemanticUseStatementCollection) {
            $importItem = $semanticNode->getByFullyQualifiedName($this->oldFullyQualifiedName);
            if ($importItem !== null) {
                $groupPrefix = $importItem->groupPrefix();
                if ($groupPrefix === null) {
                    $downgradedFullyQualifiedName = new QualifiedName($this->newFullyQualifiedName->parts());
                    $refactoringStack->add(new QualifiedNameChanger($importItem->name()->qualifiedNameNode(), $downgradedFullyQualifiedName));
                }
            }
        }
    }

    public function leave(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
    }
}