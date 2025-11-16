<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Model\SemanticNodeTransformer;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;

final class SemanticNodeTransformationExecutor
{
    /**
     * @param list<SemanticNodeTransformer> $semanticNodeTransformers
     */
    public function __construct(
        private readonly array $semanticNodeTransformers,
    ) {
    }

    public function execute(SemanticNode $semanticNode): void
    {
        $refactoringStack = new RefactoringStack();
        $this->collect($semanticNode, $refactoringStack);

        $refactoringStack->apply();
    }

    public function collect(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void
    {
        foreach ($this->semanticNodeTransformers as $semanticNodeTransformer) {
            $semanticNodeTransformer->enter($semanticNode, $refactoringStack);
        }

        foreach ($semanticNode->children() as $child) {
            $this->collect($child, $refactoringStack);
        }

        foreach ($this->semanticNodeTransformers as $semanticNodeTransformer) {
            $semanticNodeTransformer->leave($semanticNode, $refactoringStack);
        }
    }
}
