<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\PropertyDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Type\NamedTypeNode;

final class SemanticPropertyDecleration extends SemanticClassMember
{
    public function __construct(
        ClassScope $classScope,
        PropertyDeclarationNode $propertyDeclarationNode,
    ) {
        parent::__construct($classScope, $propertyDeclarationNode);
    }

    public function propertyDeclarationNode(): PropertyDeclarationNode
    {
        if (!$this->memberNode instanceof PropertyDeclarationNode) {
            throw new \InvalidArgumentException('Property declaration node must be a PropertyDeclarationNode');
        }

        return $this->memberNode;
    }

    public function __toString(): string
    {
        return 'Property: ' . $this->propertyDeclarationNode()->properties()[0]->name()->__toString();
    }

    public function children(): array
    {
        $children = [];

        $type = $this->propertyDeclarationNode()->type();
        if ($type instanceof NamedTypeNode) {
            $children[] = new SemanticQualifiedNameNode($this->classScope->namespaceScope(), $type->name(), NameKind::Property);
        }

        return [
            ...$children,
        ];
    }
}
