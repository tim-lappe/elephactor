<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Name;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class SemanticIdentifierNode extends AbstractSemanticNode
{
    public function __construct(
        private readonly NamespacedScope $namespaceScope,
        private readonly IdentifierNode $identifierNode,
    ) {
    }

    public function namespaceScope(): NamespacedScope
    {
        return $this->namespaceScope;
    }

    public function identifierNode(): IdentifierNode
    {
        return $this->identifierNode;
    }

    public function identifier(): Identifier
    {
        return $this->identifierNode->identifier();
    }

    public function qualifiedName(): QualifiedName
    {
        return new QualifiedName([$this->identifier()]);
    }

    public function fullyQualifiedName(): FullyQualifiedName
    {
        return $this->namespaceScope->resolveQualifiedName(new QualifiedName([$this->identifier()]));
    }

    public function children(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return 'Identifier: ' . $this->identifier()->__toString();
    }
}
