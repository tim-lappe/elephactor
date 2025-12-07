<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Visibility;

final class TraitAliasAdaptationNode extends AbstractNode implements TraitAdaptationNode
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

        $this->children()->add('method', $method);

        if ($alias !== null) {
            $this->children()->add('alias', $alias);
        }

        if ($trait !== null) {
            $this->children()->add('trait', $trait);
        }
    }

    public function method(): TraitMethodIdentifierNode
    {
        return $this->children()->getOne('method', TraitMethodIdentifierNode::class) ?? throw new \RuntimeException('Trait method not found');
    }

    public function alias(): ?TraitAliasIdentifierNode
    {
        return $this->children()->getOne('alias', TraitAliasIdentifierNode::class);
    }

    public function visibility(): ?Visibility
    {
        return $this->visibility;
    }

    public function traitName(): ?TraitQualifiedNameNode
    {
        return $this->children()->getOne('trait', TraitQualifiedNameNode::class);
    }
}
