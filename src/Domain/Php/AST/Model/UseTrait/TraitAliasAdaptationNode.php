<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Visibility;

final readonly class TraitAliasAdaptationNode extends AbstractNode implements TraitAdaptationNode
{
    public function __construct(
        Identifier $method,
        ?Identifier $alias = null,
        private readonly ?Visibility $visibility = null,
        ?QualifiedName $trait = null
    ) {
        parent::__construct();

        $method = new TraitMethodIdentifierNode($method);
        $alias = $alias !== null ? new TraitAliasIdentifierNode($alias) : null;
        $trait = $trait !== null ? new TraitQualifiedNameNode($trait) : null;

        $this->children()->add($method);
        
        if ($alias !== null) {
            $this->children()->add($alias);
        }
        
        if ($trait !== null) {
            $this->children()->add($trait);
        }
    }

    public function method(): TraitMethodIdentifierNode
    {
        return $this->children()->firstOfType(TraitMethodIdentifierNode::class) ?? throw new \RuntimeException('Trait method not found');
    }

    public function alias(): ?TraitAliasIdentifierNode
    {
        return $this->children()->firstOfType(TraitAliasIdentifierNode::class);
    }

    public function visibility(): ?Visibility
    {
        return $this->visibility;
    }

    public function traitName(): ?TraitQualifiedNameNode
    {
        return $this->children()->firstOfType(TraitQualifiedNameNode::class);
    }
}
