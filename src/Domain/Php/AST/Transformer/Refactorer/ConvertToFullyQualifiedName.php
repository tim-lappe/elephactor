<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

final class ConvertToFullyQualifiedName implements Refactoring
{
    public function __construct(
        private readonly NamespaceDefinitionNode $namespaceNode,
        private readonly QualifiedNameNode $qualifiedNameNode,
    ) {
    }

    public function apply(): void
    {
        $namespaceScope = $this->namespaceNode->name()->qualifiedName();
        $newQualifiedName = new FullyQualifiedName(
            $namespaceScope
                ->extend($this->qualifiedNameNode->qualifiedName()->lastPart())
                ->parts(),
        );
        $this->qualifiedNameNode->replaceQualifiedName($newQualifiedName);
    }

    public function isApplicable(): bool
    {
        return !$this->qualifiedNameNode->qualifiedName() instanceof FullyQualifiedName;
    }
}
