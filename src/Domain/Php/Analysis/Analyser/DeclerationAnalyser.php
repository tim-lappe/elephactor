<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticClassLikeDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticMethodDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticTraitUsage;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\ClassDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\EnumDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\InterfaceDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\TraitDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;

final class DeclerationAnalyser
{
    public function __construct(
        private readonly ClassDeclerationAnalyser $classDeclerationAnalyser,
        private readonly InterfaceDeclerationAnalyser $interfaceDeclerationAnalyser,
        private readonly TraitDeclerationAnalyser $traitDeclerationAnalyser,
        private readonly EnumDeclerationAnalyser $enumDeclerationAnalyser,
        private readonly BodyUsageAnalyser $bodyUsageAnalyser,
    ) {
    }

    public function analyse(SemanticFileNode $fileNode): void
    {
        $this->analyseNodes(
            $fileNode,
            new NamespacedScope($fileNode->fileScope(), new PhpNamespace()),
            $fileNode->fileNode()->children(),
        );
    }

    /**
     * @param list<Node> $nodes
     */
    private function analyseNodes(SemanticFileNode $fileNode, NamespacedScope $namespaceScope, array $nodes): void
    {
        $currentScope = $namespaceScope;

        foreach ($nodes as $node) {
            if ($node instanceof NamespaceDefinitionNode) {
                $namespace = new PhpNamespace($node->name()->qualifiedName());
                $namespacedScope = new NamespacedScope($fileNode->fileScope(), $namespace);

                $this->analyseNodes($fileNode, $namespacedScope, $node->statements());

                if ($node->isBracketed()) {
                    $currentScope = new NamespacedScope($fileNode->fileScope(), new PhpNamespace());
                    continue;
                }

                $currentScope = $namespacedScope;
                continue;
            }

            $this->analyseNode($fileNode, $currentScope, $node);
        }
    }

    private function analyseNode(SemanticFileNode $fileNode, NamespacedScope $namespaceScope, Node $node): void
    {
        if ($node instanceof ClassDeclarationNode) {
            $semanticDecleration = $this->classDeclerationAnalyser->analyse($fileNode, $namespaceScope, $node);
            $this->analyseMembers($semanticDecleration, $node->members());
            return;
        }

        if ($node instanceof InterfaceDeclarationNode) {
            $semanticDecleration = $this->interfaceDeclerationAnalyser->analyse($fileNode, $namespaceScope, $node);
            $this->analyseMembers($semanticDecleration, $node->members());
            return;
        }

        if ($node instanceof TraitDeclarationNode) {
            $semanticDecleration = $this->traitDeclerationAnalyser->analyse($fileNode, $namespaceScope, $node);
            $this->analyseMembers($semanticDecleration, $node->members());
            return;
        }

        if ($node instanceof EnumDeclarationNode) {
            $semanticDecleration = $this->enumDeclerationAnalyser->analyse($fileNode, $namespaceScope, $node);
            $this->analyseMembers($semanticDecleration, $node->members());
        }
    }

    /**
     * @param list<MemberNode> $members
     */
    private function analyseMembers(SemanticClassLikeDecleration $classLikeDeclaration, array $members): void
    {
        foreach ($classLikeDeclaration->classMembers() as $classMember) {
            if ($classMember instanceof SemanticMethodDecleration) {
                $this->bodyUsageAnalyser->analyse($classMember);
                $classLikeDeclaration->usages()->merge($classMember->usagesInMethod());
            }

            if ($classMember instanceof SemanticTraitUsage) {
                $this->analyseTraitUsage($classLikeDeclaration, $classMember);
            }
        }
    }

    private function analyseTraitUsage(SemanticClassLikeDecleration $classLikeDeclaration, SemanticTraitUsage $semanticTraitUsage): void
    {
        foreach ($semanticTraitUsage->traitNames() as $traitName) {
            $resolvedName = $classLikeDeclaration->classScope()->namespaceScope()->resolveQualifiedName($traitName->qualifiedName());
            $classLikeDeclaration->usages()->addUsage($resolvedName, $semanticTraitUsage);
        }
    }
}
