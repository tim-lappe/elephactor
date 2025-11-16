<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Attribute\SemanticClassAttribute;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\NamespacedScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\UsageMap;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticIdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\ClassDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\MethodDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\PropertyDeclarationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\TraitUseNode;

abstract class SemanticClassLikeDecleration extends AbstractSemanticNode
{
    private ClassScope $classScope;

    /**
     * @var list<SemanticClassAttribute> $attributes
     */
    private array $attributes = [];

    /**
     * @var list<SemanticInterfaceImplementation> $interfaceImplementations
     */
    private array $interfaceImplementations = [];

    /**
     * @var list<SemanticTraitUsage> $traitUsages
     */
    private array $traitUsages = [];

    /**
     * @var list<SemanticClassMember> $classMembers
     */
    private array $classMembers = [];

    private UsageMap $usages;

    public function __construct(
        private readonly ClassLikeNode $classLikeNode,
        NamespacedScope $namespaceScope
    ) {
        $this->classScope = new ClassScope($this, $namespaceScope);
        $this->usages = new UsageMap();
        $this->classMembers = [];

        if ($this->classLikeNode instanceof ClassDeclarationNode) {
            foreach ($this->classLikeNode->members() as $member) {
                if ($member instanceof MethodDeclarationNode) {
                    $this->classMembers[] = new SemanticMethodDecleration($this->classScope, $member);
                }

                if ($member instanceof PropertyDeclarationNode) {
                    $this->classMembers[] = new SemanticPropertyDecleration($this->classScope, $member);
                }

                if ($member instanceof TraitUseNode) {
                    $this->classMembers[] = new SemanticTraitUsage($this->classScope, $member);
                }
            }
        }
    }

    public function addMethodDecleration(SemanticMethodDecleration $methodDecleration): void
    {
        $this->classMembers[] = $methodDecleration;
    }

    /**
     * @return list<SemanticClassMember>
     */
    public function classMembers(): array
    {
        return $this->classMembers;
    }

    public function name(): SemanticIdentifierNode
    {
        return new SemanticIdentifierNode($this->classScope->namespaceScope(), $this->classLikeNode->name());
    }

    public function fileNode(): SemanticFileNode
    {
        return $this->classScope->namespaceScope()->parentScope()->fileNode();
    }

    public function classScope(): ClassScope
    {
        return $this->classScope;
    }

    public function astNode(): ClassLikeNode
    {
        return $this->classLikeNode;
    }

    public function addAttribute(SemanticClassAttribute $attribute): void
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @return list<SemanticClassAttribute>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function addInterfaceImplementation(SemanticInterfaceImplementation $interfaceImplementation): void
    {
        $this->interfaceImplementations[] = $interfaceImplementation;
    }

    /**
     * @return list<SemanticInterfaceImplementation>
     */
    public function interfaceImplementations(): array
    {
        return $this->interfaceImplementations;
    }

    public function addTraitUsage(SemanticTraitUsage $traitUsage): void
    {
        $this->traitUsages[] = $traitUsage;
    }

    /**
     * @return list<SemanticTraitUsage>
     */
    public function traitUsages(): array
    {
        return $this->traitUsages;
    }

    public function usages(): UsageMap
    {
        return $this->usages;
    }

    public function children(): array
    {
        return [
            ...parent::children(),
            $this->name(),
            ...$this->attributes,
            ...$this->interfaceImplementations,
            ...$this->traitUsages,
            ...$this->classMembers,
        ];
    }

    public function __toString(): string
    {
        return 'ClassLike: ' . $this->name()->__toString();
    }
}
