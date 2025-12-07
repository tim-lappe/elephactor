<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class TraitPrecedenceAdaptationNode extends AbstractNode implements TraitAdaptationNode
{
    /**
     * @param list<QualifiedName> $insteadOf
     */
    public function __construct(
        QualifiedName $originatingTrait,
        Identifier $method,
        array $insteadOf
    ) {
        parent::__construct();

        if ($insteadOf === []) {
            throw new \InvalidArgumentException('Trait precedence adaptation requires at least one replacement trait');
        }

        $originatingTrait = new TraitQualifiedNameNode($originatingTrait);
        $method = new TraitMethodIdentifierNode($method);
        $insteadOf = array_map(
            fn (QualifiedName $qualifiedName): TraitInsteadOfQualifiedNameNode => new TraitInsteadOfQualifiedNameNode($qualifiedName),
            $insteadOf,
        );

        $this->children()->add('originatingTrait', $originatingTrait);
        $this->children()->add('method', $method);
        foreach ($insteadOf as $insteadOfTrait) {
            $this->children()->add('insteadOfTrait', $insteadOfTrait);
        }
    }

    public function originatingTrait(): TraitQualifiedNameNode
    {
        return $this->children()->getOne('originatingTrait', TraitQualifiedNameNode::class) ?? throw new \RuntimeException('Trait originating trait not found');
    }

    public function method(): TraitMethodIdentifierNode
    {
        return $this->children()->getOne('method', TraitMethodIdentifierNode::class) ?? throw new \RuntimeException('Trait method not found');
    }

    /**
     * @return list<TraitInsteadOfQualifiedNameNode>
     */
    public function insteadOf(): array
    {
        return $this->children()->getAllOf('insteadOfTrait', TraitInsteadOfQualifiedNameNode::class);
    }
}
