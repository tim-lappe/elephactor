<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Attribute\SemanticClassAttribute;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticInterfaceDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticInterfaceExtends;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\InterfaceDeclarationNode;

final class InterfaceDeclerationAnalyser
{
    public function analyse(SemanticFileNode $fileNode, NamespacedScope $currentNamespaceScope, InterfaceDeclarationNode $interfaceDeclarationNode): SemanticInterfaceDecleration
    {
        $semanticInterfaceDecleration = new SemanticInterfaceDecleration($interfaceDeclarationNode, $currentNamespaceScope);
        $fileNode->addClassLikeDecleration($semanticInterfaceDecleration);

        $this->analyseAttributes($semanticInterfaceDecleration, $interfaceDeclarationNode);
        $this->analyseExtends($semanticInterfaceDecleration, $interfaceDeclarationNode);

        return $semanticInterfaceDecleration;
    }

    private function analyseAttributes(SemanticInterfaceDecleration $semanticInterfaceDecleration, InterfaceDeclarationNode $interfaceDeclarationNode): void
    {
        foreach ($interfaceDeclarationNode->attributes() as $attributeGroup) {
            foreach ($attributeGroup->attributes() as $attribute) {
                $semanticAttribute = new SemanticClassAttribute($semanticInterfaceDecleration->classScope(), $attribute);
                $semanticInterfaceDecleration->addAttribute($semanticAttribute);

                $resolvedName = $semanticInterfaceDecleration->classScope()->namespaceScope()->resolveQualifiedName($attribute->name()->qualifiedName());
                $semanticInterfaceDecleration->usages()->addUsage($resolvedName, $semanticAttribute);
            }
        }
    }

    private function analyseExtends(SemanticInterfaceDecleration $semanticInterfaceDecleration, InterfaceDeclarationNode $interfaceDeclarationNode): void
    {
        foreach ($interfaceDeclarationNode->extends() as $extend) {
            $resolvedName = $semanticInterfaceDecleration->classScope()->namespaceScope()->resolveQualifiedName($extend->qualifiedName());
            $semanticInterfaceExtends = new SemanticInterfaceExtends($semanticInterfaceDecleration, $extend);
            $semanticInterfaceDecleration->addExtends($semanticInterfaceExtends);

            $semanticInterfaceDecleration->usages()->addUsage($resolvedName, $semanticInterfaceExtends);
        }
    }
}
