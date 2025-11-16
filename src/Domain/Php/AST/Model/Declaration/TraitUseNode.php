<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait\TraitAdaptationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class TraitUseNode extends AbstractNode implements MemberNode
{
    /**
     * @var list<QualifiedNameNode>
     */
    private readonly array $traits;
    /**
     * @param list<QualifiedName>       $traits
     * @param list<TraitAdaptationNode> $adaptations
     */
    public function __construct(
        array $traits,
        private readonly array $adaptations = []
    ) {
        if ($traits === []) {
            throw new \InvalidArgumentException('Trait use requires at least one trait');
        }

        parent::__construct(NodeKind::TRAIT_USE);

        $this->traits = array_map(
            fn (QualifiedName $trait): QualifiedNameNode => new QualifiedNameNode($trait, $this),
            $traits,
        );
    }

    /**
     * @return list<QualifiedNameNode>
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
        return [
            ...$this->traits,
            ...$this->adaptations,
        ];
    }
}
