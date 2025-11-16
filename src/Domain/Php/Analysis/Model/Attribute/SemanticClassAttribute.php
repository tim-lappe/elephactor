<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;

final class SemanticClassAttribute extends AbstractSemanticNode
{
    private SemanticQualifiedNameNode $semanticNameNode;

    public function __construct(
        private readonly ClassScope $classScope,
        private readonly AttributeNode $attributeNode,
    ) {
        $this->semanticNameNode = new SemanticQualifiedNameNode($this->classScope->namespaceScope(), $this->attributeNode->name(), NameKind::Attribute);
    }

    public function classScope(): ClassScope
    {
        return $this->classScope;
    }

    public function astNode(): AttributeNode
    {
        return $this->attributeNode;
    }

    public function name(): SemanticQualifiedNameNode
    {
        return $this->semanticNameNode;
    }

    public function children(): array
    {
        return [$this->semanticNameNode];
    }

    public function __toString(): string
    {
        return 'Attribute: ' . $this->semanticNameNode->__toString();
    }
}
