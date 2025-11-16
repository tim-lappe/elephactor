<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Name;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;

final class SemanticQualifiedNameNode extends AbstractSemanticNode
{
    public function __construct(
        private readonly NamespacedScope $namespaceScope,
        private readonly QualifiedNameNode $qualifiedNameNode,
        private readonly NameKind $nameKind,
    ) {
    }

    public function nameKind(): NameKind
    {
        return $this->nameKind;
    }

    public function namespaceScope(): NamespacedScope
    {
        return $this->namespaceScope;
    }

    public function qualifiedNameNode(): QualifiedNameNode
    {
        return $this->qualifiedNameNode;
    }

    public function qualifiedName(): QualifiedName
    {
        return $this->qualifiedNameNode->qualifiedName();
    }

    public function fullyQualifiedName(): FullyQualifiedName
    {
        return $this->namespaceScope->resolveQualifiedName($this->qualifiedName());
    }

    public function children(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return 'QualifiedName: ' . $this->qualifiedName()->__toString();
    }
}
