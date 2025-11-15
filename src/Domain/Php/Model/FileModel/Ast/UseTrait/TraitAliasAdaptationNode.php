<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\UseTrait;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Visibility;

final class TraitAliasAdaptationNode extends AbstractNode implements TraitAdaptationNode
{
    public function __construct(
        private readonly Identifier $method,
        private readonly ?Identifier $alias = null,
        private readonly ?Visibility $visibility = null,
        private readonly ?QualifiedName $trait = null
    ) {
        parent::__construct(NodeKind::TRAIT_ALIAS_ADAPTATION);
    }

    public function method(): Identifier
    {
        return $this->method;
    }

    public function alias(): ?Identifier
    {
        return $this->alias;
    }

    public function visibility(): ?Visibility
    {
        return $this->visibility;
    }

    public function traitName(): ?QualifiedName
    {
        return $this->trait;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
