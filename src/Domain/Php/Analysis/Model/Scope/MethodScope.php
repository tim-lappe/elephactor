<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticMethodDecleration;

final class MethodScope
{
    public function __construct(
        private readonly SemanticMethodDecleration $methodDeclaration,
        private readonly ClassScope $classScope,
    ) {
    }

    public function classScope(): ClassScope
    {
        return $this->classScope;
    }

    public function methodDeclaration(): SemanticMethodDecleration
    {
        return $this->methodDeclaration;
    }
}
