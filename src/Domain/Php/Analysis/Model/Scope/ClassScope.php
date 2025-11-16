<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticClassLikeDecleration;

final class ClassScope
{
    public function __construct(
        private readonly SemanticClassLikeDecleration $classLikeDeclaration,
        private readonly NamespacedScope $parentScope,
    ) {
    }

    public function namespaceScope(): NamespacedScope
    {
        return $this->parentScope;
    }

    public function classLikeDeclaration(): SemanticClassLikeDecleration
    {
        return $this->classLikeDeclaration;
    }
}
