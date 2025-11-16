<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;

final class SemanticInterfaceExtends extends AbstractSemanticNode
{
    private SemanticQualifiedNameNode $semanticNameNode;

    public function __construct(
        private readonly SemanticInterfaceDecleration $semanticInterfaceDecleration,
        private readonly QualifiedNameNode $extends,
    ) {
        $this->semanticNameNode = new SemanticQualifiedNameNode($this->semanticInterfaceDecleration->classScope()->namespaceScope(), $this->extends, NameKind::InterfaceExtends);
    }

    public function semanticInterfaceDecleration(): SemanticInterfaceDecleration
    {
        return $this->semanticInterfaceDecleration;
    }

    public function astNode(): ClassLikeNode
    {
        return $this->semanticInterfaceDecleration->astNode();
    }

    public function extends(): SemanticQualifiedNameNode
    {
        return $this->semanticNameNode;
    }

    public function children(): array
    {
        return [$this->semanticNameNode];
    }

    public function __toString(): string
    {
        return 'Extends: ' . $this->semanticNameNode->__toString();
    }
}
