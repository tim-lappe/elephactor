<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\UseTrait\TraitAdaptationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class TraitUseNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<QualifiedName>       $traits
     * @param list<TraitAdaptationNode> $adaptations
     */
    public function __construct(
        private readonly array $traits,
        private readonly array $adaptations = []
    ) {
        if ($traits === []) {
            throw new \InvalidArgumentException('Trait use requires at least one trait');
        }

        parent::__construct(NodeKind::TRAIT_USE);
    }

    /**
     * @return list<QualifiedName>
     */
    public function traits(): array
    {
        return $this->traits;
    }

    /**
     * @return list<TraitAdaptationNode>
     */
    public function adaptations(): array
    {
        return $this->adaptations;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->adaptations;
    }
}
