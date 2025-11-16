<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Attribute\SemanticClassAttribute;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticTraitDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\TraitDeclarationNode;

final class TraitDeclerationAnalyser
{
    public function analyse(SemanticFileNode $fileNode, NamespacedScope $currentNamespaceScope, TraitDeclarationNode $traitDeclarationNode): SemanticTraitDecleration
    {
        $semanticTraitDecleration = new SemanticTraitDecleration($traitDeclarationNode, $currentNamespaceScope);
        $fileNode->addClassLikeDecleration($semanticTraitDecleration);

        $this->analyseAttributes($semanticTraitDecleration, $traitDeclarationNode);

        return $semanticTraitDecleration;
    }

    private function analyseAttributes(SemanticTraitDecleration $semanticTraitDecleration, TraitDeclarationNode $traitDeclarationNode): void
    {
        foreach ($traitDeclarationNode->attributes() as $attributeGroup) {
            foreach ($attributeGroup->attributes() as $attribute) {
                $semanticAttribute = new SemanticClassAttribute($semanticTraitDecleration->classScope(), $attribute);
                $semanticTraitDecleration->addAttribute($semanticAttribute);

                $resolvedName = $semanticTraitDecleration->classScope()->namespaceScope()->resolveQualifiedName($attribute->name()->qualifiedName());
                $semanticTraitDecleration->usages()->addUsage($resolvedName, $semanticAttribute);
            }
        }
    }
}
