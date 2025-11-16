<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Visibility;

final class TraitAliasAdaptationNode extends AbstractNode implements TraitAdaptationNode
{
    private IdentifierNode $method;
    private readonly ?IdentifierNode $alias;
    private readonly ?QualifiedNameNode $trait;

    public function __construct(
        Identifier $method,
        ?Identifier $alias = null,
        private readonly ?Visibility $visibility = null,
        ?QualifiedName $trait = null
    ) {
        parent::__construct(NodeKind::TRAIT_ALIAS_ADAPTATION);

        $this->method = new IdentifierNode($method, $this);
        $this->alias = $alias !== null ? new IdentifierNode($alias, $this) : null;
        $this->trait = $trait !== null ? new QualifiedNameNode($trait, $this) : null;
    }

    public function method(): IdentifierNode
    {
        return $this->method;
    }

    public function alias(): ?IdentifierNode
    {
        return $this->alias;
    }

    public function visibility(): ?Visibility
    {
        return $this->visibility;
    }

    public function traitName(): ?QualifiedNameNode
    {
        return $this->trait;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return array_values(array_filter([
            $this->method,
            $this->alias,
            $this->trait,
        ]));
    }
}
