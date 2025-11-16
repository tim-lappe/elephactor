<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Model;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer\RefactoringStack;

interface SemanticNodeTransformer
{
    public function enter(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void;

    public function leave(SemanticNode $semanticNode, RefactoringStack $refactoringStack): void;
}
