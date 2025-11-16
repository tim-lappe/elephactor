<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Usage;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticMethodDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;

final class SemanticClassLikeUsage extends AbstractSemanticNode
{
    private SemanticQualifiedNameNode $semanticNameNode;

    public function __construct(
        private readonly SemanticMethodDecleration $methodDecleration,
        private readonly QualifiedNameNode $referencedName,
    ) {
        $this->semanticNameNode = new SemanticQualifiedNameNode(
            $this->methodDecleration->methodScope()->classScope()->namespaceScope(),
            $this->referencedName,
            NameKind::ClassLikeUsage,
        );
    }

    public function methodDeclaration(): SemanticMethodDecleration
    {
        return $this->methodDecleration;
    }

    public function referencedName(): SemanticQualifiedNameNode
    {
        return $this->semanticNameNode;
    }

    public function children(): array
    {
        return [$this->semanticNameNode];
    }

    public function __toString(): string
    {
        return 'ClassLikeUsage: ' . $this->semanticNameNode->__toString();
    }
}
