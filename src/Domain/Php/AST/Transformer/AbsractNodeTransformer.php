<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer;

use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\RefactoringResult;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\RefactoringStack;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\NodeVisitor;

abstract class AbsractNodeTransformer implements NodeVisitor
{
    protected RefactoringStack $refactorings;

    public function __construct()
    {
        $this->refactorings = new RefactoringStack();
    }

    public function apply(): RefactoringResult
    {
        return $this->refactorings->apply();
    }
}
