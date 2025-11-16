<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Attribute\SemanticClassAttribute;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticEnumDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticInterfaceImplementation;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\EnumDeclarationNode;

final class EnumDeclerationAnalyser
{
    public function analyse(SemanticFileNode $fileNode, NamespacedScope $currentNamespaceScope, EnumDeclarationNode $enumDeclarationNode): SemanticEnumDecleration
    {
        $semanticEnumDecleration = new SemanticEnumDecleration($enumDeclarationNode, $currentNamespaceScope);
        $fileNode->addClassLikeDecleration($semanticEnumDecleration);

        $this->analyseAttributes($semanticEnumDecleration, $enumDeclarationNode);
        $this->analyseImplements($semanticEnumDecleration, $enumDeclarationNode);

        return $semanticEnumDecleration;
    }

    private function analyseAttributes(SemanticEnumDecleration $semanticEnumDecleration, EnumDeclarationNode $enumDeclarationNode): void
    {
        foreach ($enumDeclarationNode->attributes() as $attributeGroup) {
            foreach ($attributeGroup->attributes() as $attribute) {
                $semanticAttribute = new SemanticClassAttribute($semanticEnumDecleration->classScope(), $attribute);
                $semanticEnumDecleration->addAttribute($semanticAttribute);

                $resolvedName = $semanticEnumDecleration->classScope()->namespaceScope()->resolveQualifiedName($attribute->name()->qualifiedName());
                $semanticEnumDecleration->usages()->addUsage($resolvedName, $semanticAttribute);
            }
        }
    }

    private function analyseImplements(SemanticEnumDecleration $semanticEnumDecleration, EnumDeclarationNode $enumDeclarationNode): void
    {
        foreach ($enumDeclarationNode->implements() as $interface) {
            $resolvedName = $semanticEnumDecleration->classScope()->namespaceScope()->resolveQualifiedName($interface->qualifiedName());
            $semanticInterfaceImplementation = new SemanticInterfaceImplementation($semanticEnumDecleration, $interface);
            $semanticEnumDecleration->addInterfaceImplementation($semanticInterfaceImplementation);

            $semanticEnumDecleration->usages()->addUsage($resolvedName, $semanticInterfaceImplementation);
        }
    }
}
