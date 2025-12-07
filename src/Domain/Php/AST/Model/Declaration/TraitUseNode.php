<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait\TraitAdaptationNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class TraitUseNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<QualifiedName>       $traits
     * @param list<TraitAdaptationNode> $adaptations
     */
    public function __construct(
        array $traits,
        array $adaptations = []
    ) {
        parent::__construct();

        foreach ($traits as $trait) {
            $this->children()->add("trait", new QualifiedNameNode($trait));
        }

        foreach ($adaptations as $adaptation) {
            $this->children()->add("adaptation", $adaptation);
        }
    }

    /**
     * @return list<QualifiedNameNode>
     */
    public function traits(): array
    {
        return $this->children()->getAllOf("trait", QualifiedNameNode::class);
    }

    /**
     * @return list<TraitAdaptationNode>
     */
    public function adaptations(): array
    {
        return $this->children()->getAllOf("adaptation", TraitAdaptationNode::class);
    }
}
