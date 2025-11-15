<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Resolution\ClassReference;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AliasMap;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\FileNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClass;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier as AstIdentifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\ClassDeclarationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\InterfaceDeclarationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\EnumDeclarationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\TraitUseNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\AnonymousClassExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\ClassConstantFetchExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\InstanceofExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\NewExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\StaticCallExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\StaticPropertyFetchExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Type\NamedTypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\UseTrait\TraitAliasAdaptationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\UseTrait\TraitPrecedenceAdaptationNode;

final class ClassReferenceFinder
{
    public function __construct(
        private readonly PhpClassCollection $classCollection,
    ) {
    }

    /**
     * @return list<ClassReference>
     */
    public function findClassReferences(PhpClass $targetClass): array
    {
        $references = [];
        $visitedFiles = [];
        $targetFullName = $targetClass->fullyQualifiedIdentifier();

        foreach ($this->classCollection->toArray() as $phpClass) {
            $file = $phpClass->file();
            $fileId = spl_object_id($file);

            if (isset($visitedFiles[$fileId])) {
                continue;
            }

            $visitedFiles[$fileId] = true;
            $currentNamespace = $phpClass->namespace();

            $fileNode = $file->fileNode();
            $aliasMap = $this->buildAliasMap($fileNode);
            $referenceNodes = $this->collectQualifiedNameReferences(
                $fileNode,
                $currentNamespace,
                $aliasMap,
                $targetFullName,
            );

            if ($referenceNodes !== []) {
                $references[] = new ClassReference($file, $referenceNodes);
            }
        }

        return $references;
    }

    /**
     * @return list<QualifiedName>
     */
    private function collectQualifiedNameReferences(
        FileNode $fileNode,
        ?PhpNamespace $currentNamespace,
        AliasMap $aliasMap,
        FullyQualifiedName $targetFullName,
    ): array {
        $references = [];
        $nodes = $fileNode->children();

        while ($nodes !== []) {
            $node = array_shift($nodes);
            $references = [...$references, ...$this->matchNodeReferences($node, $currentNamespace, $aliasMap, $targetFullName)];

            foreach ($node->children() as $child) {
                $nodes[] = $child;
            }
        }

        return $references;
    }

    /**
     * @param  AliasMap            $aliasMap
     * @return list<QualifiedName>
     */
    private function matchNodeReferences(
        Node $node,
        ?PhpNamespace $currentNamespace,
        AliasMap $aliasMap,
        FullyQualifiedName $targetFullName,
    ): array {

        /** @var list<QualifiedName> $references */
        $references = [];

        if ($node instanceof ClassDeclarationNode) {
            $extends = $node->extends();
            if ($extends !== null) {
                $resolvedName = $extends->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $extends;
                }
            }

            foreach ($node->interfaces() as $interface) {
                $resolvedName = $interface->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $interface;
                }
            }
        }

        if ($node instanceof InterfaceDeclarationNode) {
            foreach ($node->extends() as $extend) {
                $resolvedName = $extend->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $extend;
                }
            }
        }

        if ($node instanceof EnumDeclarationNode) {
            foreach ($node->implements() as $implement) {
                $resolvedName = $implement->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $implement;
                }
            }
        }

        if ($node instanceof AnonymousClassExpressionNode) {
            $extends = $node->extends();
            if ($extends !== null) {
                $resolvedName = $extends->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $extends;
                }
            }

            foreach ($node->interfaces() as $interface) {
                $resolvedName = $interface->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $interface;
                }
            }
        }

        if ($node instanceof TraitUseNode) {
            foreach ($node->traits() as $trait) {
                $resolvedName = $trait->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $trait;
                }
            }
        }

        if ($node instanceof TraitAliasAdaptationNode) {
            $traitName = $node->traitName();
            if ($traitName !== null) {
                $resolvedName = $traitName->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $traitName;
                }
            }
        }

        if ($node instanceof TraitPrecedenceAdaptationNode) {
            $originatingTrait = $node->originatingTrait();
            $resolvedName = $originatingTrait->resolve($currentNamespace, $aliasMap);
            if ($resolvedName->equals($targetFullName)) {
                $references[] = $originatingTrait;
            }

            foreach ($node->insteadOf() as $insteadOf) {
                $resolvedName = $insteadOf->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $insteadOf;
                }
            }
        }

        if ($node instanceof AttributeNode) {
            $attributeName = $node->name();
            $resolvedName = $attributeName->resolve($currentNamespace, $aliasMap);
            if ($resolvedName->equals($targetFullName)) {
                $references[] = $attributeName;
            }
        }

        if ($node instanceof NewExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedName) {
                $resolvedName = $classReference->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $classReference;
                }
            }
        }

        if ($node instanceof StaticCallExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedName) {
                $resolvedName = $classReference->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $classReference;
                }
            }
        }

        if ($node instanceof StaticPropertyFetchExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedName) {
                $resolvedName = $classReference->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $classReference;
                }
            }
        }

        if ($node instanceof ClassConstantFetchExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedName) {
                $resolvedName = $classReference->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $classReference;
                }
            }
        }

        if ($node instanceof InstanceofExpressionNode) {
            $classReference = $node->classReference();
            if ($classReference instanceof QualifiedName) {
                $resolvedName = $classReference->resolve($currentNamespace, $aliasMap);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $classReference;
                }
            }
        }

        if ($node instanceof NamedTypeNode && !$node->name()->isReservedTypeName()) {
            $resolvedName = $node->name()->resolve($currentNamespace, $aliasMap);
            if ($resolvedName->equals($targetFullName)) {
                $references[] = $node->name();
            }
        }

        if ($node instanceof UseStatementNode) {
            foreach ($node->clauses() as $clause) {
                $parts = $this->resolveUseClauseParts($node, $clause->name());
                $resolvedName = new FullyQualifiedName($parts);
                if ($resolvedName->equals($targetFullName)) {
                    $references[] = $clause->name();
                }
            }
        }

        return $references;
    }

    private function buildAliasMap(FileNode $fileNode): AliasMap
    {
        $aliasMap = new AliasMap();
        foreach ($fileNode->useStatements()->filterKind(UseKind::CLASS_IMPORT)->toArray() as $useStatement) {
            $aliasMap->merge($this->extractAliasesFromUseStatement($useStatement));
        }
        return $aliasMap;
    }

    private function extractAliasesFromUseStatement(UseStatementNode $useStatement): AliasMap
    {
        $aliasMap = new AliasMap();
        foreach ($useStatement->clauses() as $clause) {
            $alias = $clause->alias() ?? $clause->name()->lastPart();
            $parts = $this->resolveUseClauseParts($useStatement, $clause->name());
            $aliasMap->add($alias, new QualifiedName($parts, true));
        }

        return $aliasMap;
    }

    /**
     * @return list<AstIdentifier>
     */
    private function resolveUseClauseParts(UseStatementNode $useStatement, QualifiedName $clauseName): array
    {
        $parts = $clauseName->parts();
        $groupPrefix = $useStatement->groupPrefix();

        if ($groupPrefix === null) {
            return $parts;
        }

        return [...$groupPrefix->parts(), ...$parts];
    }
}
