<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;

final class SemanticInterfaceImplementation extends AbstractSemanticNode
{
    private SemanticQualifiedNameNode $semanticNameNode;

    public function __construct(
        private readonly SemanticClassLikeDecleration $semanticClassLikeDecleration,
        private readonly QualifiedNameNode $interfaceName,
    ) {
        $this->semanticNameNode = new SemanticQualifiedNameNode($this->semanticClassLikeDecleration->classScope()->namespaceScope(), $this->interfaceName, NameKind::InterfaceImplementation);
    }

    public function semanticClassLikeDecleration(): SemanticClassLikeDecleration
    {
        return $this->semanticClassLikeDecleration;
    }

    public function astNode(): ClassLikeNode
    {
        return $this->semanticClassLikeDecleration->astNode();
    }

    public function interfaceName(): SemanticQualifiedNameNode
    {
        return $this->semanticNameNode;
    }

    public function children(): array
    {
        return [...parent::children(), $this->semanticNameNode];
    }

    public function __toString(): string
    {
        return 'Implementation: ' . $this->semanticNameNode->__toString();
    }
}
