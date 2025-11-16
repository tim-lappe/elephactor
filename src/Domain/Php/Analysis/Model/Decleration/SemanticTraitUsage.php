<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\NameKind;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\TraitUseNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait\TraitAliasAdaptationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait\TraitPrecedenceAdaptationNode;

final class SemanticTraitUsage extends SemanticClassMember
{
    /**
     * @var list<SemanticQualifiedNameNode>
     */
    private array $traitNames = [];

    /**
     * @var list<SemanticQualifiedNameNode> $adaptationTraitNames
     */
    private array $adaptationTraitNames = [];

    public function __construct(
        ClassScope $classScope,
        TraitUseNode $traitUseNode,
    ) {
        parent::__construct($classScope, $traitUseNode);
        $this->traitNames = array_map(fn (QualifiedNameNode $trait) => new SemanticQualifiedNameNode($classScope->namespaceScope(), $trait, NameKind::TraitUsage), $traitUseNode->traits());

        foreach ($traitUseNode->adaptations() as $adaptation) {
            if ($adaptation instanceof TraitAliasAdaptationNode) {
                $traitName = $adaptation->traitName();
                if ($traitName !== null) {
                    $this->adaptationTraitNames[] = new SemanticQualifiedNameNode($classScope->namespaceScope(), $traitName, NameKind::TraitUsage);
                }
            }

            if ($adaptation instanceof TraitPrecedenceAdaptationNode) {
                foreach ($adaptation->insteadOf() as $insteadOf) {
                    $this->adaptationTraitNames[] = new SemanticQualifiedNameNode($classScope->namespaceScope(), $insteadOf, NameKind::TraitUsage);
                }

                $this->adaptationTraitNames[] = new SemanticQualifiedNameNode($classScope->namespaceScope(), $adaptation->originatingTrait(), NameKind::TraitUsage);
            }
        }
    }

    /**
     * @return list<SemanticQualifiedNameNode>
     */
    public function adaptationTraitNames(): array
    {
        return $this->adaptationTraitNames;
    }

    public function classScope(): ClassScope
    {
        return $this->classScope;
    }

    /**
     * @return list<SemanticQualifiedNameNode>
     */
    public function traitNames(): array
    {
        return $this->traitNames;
    }

    public function children(): array
    {
        return [...parent::children(), ...$this->traitNames, ...$this->adaptationTraitNames];
    }

    public function __toString(): string
    {
        return 'TraitUsage: ' . implode(', ', array_map(fn (SemanticQualifiedNameNode $traitName) => $traitName->__toString(), $this->traitNames));
    }
}
