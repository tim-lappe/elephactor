<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticMethodDecleration;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Usage\SemanticClassLikeUsage;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\UsageMap;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Expression\AnonymousClassExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Expression\ClassConstantFetchExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Expression\InstanceofExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Expression\NewExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Expression\StaticCallExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Expression\StaticPropertyFetchExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\Type\NamedTypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;

final class BodyUsageAnalyser
{
    public function analyse(SemanticMethodDecleration $methodDecleration): void
    {
        $scope = $methodDecleration->methodScope()->classScope()->namespaceScope();
        $usageMap = $methodDecleration->usagesInMethod();

        $nodes = [$methodDecleration->astNode()];
        while ($nodes !== []) {
            $node = array_shift($nodes);
            $this->collectUsagesFromNode($methodDecleration, $scope, $usageMap, $node);

            foreach ($node->children() as $child) {
                $nodes[] = $child;
            }
        }
    }

    private function collectUsagesFromNode(SemanticMethodDecleration $methodDecleration, NamespacedScope $scope, UsageMap $usageMap, Node $node): void
    {
        if ($node instanceof AttributeNode) {
            $this->recordUsage($methodDecleration, $scope, $usageMap, $node->name(), $node);
        }

        if ($node instanceof NamedTypeNode) {
            $this->recordUsage($methodDecleration, $scope, $usageMap, $node->name(), $node);
        }

        if ($node instanceof AnonymousClassExpressionNode) {
            $extends = $node->extends();
            if ($extends !== null) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $extends, $node);
            }

            foreach ($node->interfaces() as $interface) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $interface, $node);
            }
        }

        if ($node instanceof NewExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedNameNode) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $classReference, $node);
            }
        }

        if ($node instanceof StaticCallExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedNameNode) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $classReference, $node);
            }
        }

        if ($node instanceof StaticPropertyFetchExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedNameNode) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $classReference, $node);
            }
        }

        if ($node instanceof ClassConstantFetchExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedNameNode) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $classReference, $node);
            }
        }

        if ($node instanceof InstanceofExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedNameNode) {
                $this->recordUsage($methodDecleration, $scope, $usageMap, $classReference, $node);
            }
        }
    }

    private function recordUsage(SemanticMethodDecleration $methodDecleration, NamespacedScope $scope, UsageMap $usageMap, QualifiedNameNode $qualifiedNameNode, Node $ownerNode): void
    {
        if ($qualifiedNameNode->qualifiedName()->isReservedTypeName()) {
            return;
        }

        $semanticUsage = new SemanticClassLikeUsage($methodDecleration, $qualifiedNameNode);
        $usageMap->addUsage($scope->resolveQualifiedName($qualifiedNameNode->qualifiedName()), $semanticUsage);
    }
}
