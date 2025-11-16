<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\PropertyNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticIdentifierNode;

final class SemanticPropertyMember extends AbstractSemanticNode
{
    private SemanticIdentifierNode $semanticNameNode;

    public function __construct(
        private readonly ClassScope $classScope,
        private readonly PropertyNode $propertyNode,
    ) {
        $this->semanticNameNode = new SemanticIdentifierNode($this->classScope->namespaceScope(), $this->propertyNode->name());
    }

    public function __toString(): string
    {
        return 'PropertyMember: ' . $this->propertyNode->name()->__toString();
    }

    public function name(): SemanticIdentifierNode
    {
        return $this->semanticNameNode;
    }

    public function classScope(): ClassScope
    {
        return $this->classScope;
    }

    public function propertyNode(): PropertyNode
    {
        return $this->propertyNode;
    }

    public function children(): array
    {
        return [$this->semanticNameNode];
    }
}
