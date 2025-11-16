<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;

final class ConvertToFullyQualifiedName implements Refactoring
{
    public function __construct(
        private readonly NamespacedScope $namespaceScope,
        private readonly QualifiedNameNode $qualifiedNameNode,
    ) {
    }

    public function apply(): void
    {
        $fullyQualifiedName = $this->namespaceScope->resolveQualifiedName($this->qualifiedNameNode->qualifiedName());
        $this->qualifiedNameNode->changeQualifiedName($fullyQualifiedName);
    }
}