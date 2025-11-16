<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticClassDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticClassExtends;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Attribute\SemanticClassAttribute;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticInterfaceImplementation;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\ClassDeclarationNode;

final class ClassDeclerationAnalyser
{
    public function analyse(SemanticFileNode $fileNode, NamespacedScope $currentNamespaceScope, ClassDeclarationNode $classDeclarationNode): SemanticClassDecleration
    {
        $semanticClassDecleration = new SemanticClassDecleration($classDeclarationNode, $currentNamespaceScope);
        $fileNode->addClassLikeDecleration($semanticClassDecleration);

        $extends = $classDeclarationNode->extends();
        if ($extends !== null) {
            $semanticClassExtends = new SemanticClassExtends($semanticClassDecleration, $extends);
            $semanticClassDecleration->addExtends($semanticClassExtends);

            $resolvedName = $semanticClassDecleration->classScope()->namespaceScope()->resolveQualifiedName($extends->qualifiedName());
            $semanticClassDecleration->usages()->addUsage($resolvedName, $semanticClassExtends);
        }

        foreach ($classDeclarationNode->attributes() as $attributeGroups) {
            $attributes = $attributeGroups->attributes();
            foreach ($attributes as $attribute) {
                $semanticClassAttribute = new SemanticClassAttribute($semanticClassDecleration->classScope(), $attribute);
                $semanticClassDecleration->addAttribute($semanticClassAttribute);

                $resolvedName = $semanticClassDecleration->classScope()->namespaceScope()->resolveQualifiedName($attribute->name()->qualifiedName());
                $semanticClassDecleration->usages()->addUsage($resolvedName, $semanticClassAttribute);
            }
        }

        foreach ($classDeclarationNode->interfaces() as $interface) {
            $resolvedName = $semanticClassDecleration->classScope()->namespaceScope()->resolveQualifiedName($interface->qualifiedName());
            $semanticInterfaceImplementation = new SemanticInterfaceImplementation($semanticClassDecleration, $interface);
            $semanticClassDecleration->addInterfaceImplementation($semanticInterfaceImplementation);

            $semanticClassDecleration->usages()->addUsage($resolvedName, $semanticInterfaceImplementation);
        }

        return $semanticClassDecleration;
    }
}
