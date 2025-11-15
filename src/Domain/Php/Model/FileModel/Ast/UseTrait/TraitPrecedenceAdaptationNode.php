<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\UseTrait;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class TraitPrecedenceAdaptationNode extends AbstractNode implements TraitAdaptationNode
{
    /**
     * @param list<QualifiedName> $insteadOf
     */
    public function __construct(
        private readonly QualifiedName $originatingTrait,
        private readonly Identifier $method,
        private readonly array $insteadOf
    ) {
        if ($insteadOf === []) {
            throw new \InvalidArgumentException('Trait precedence adaptation requires at least one replacement trait');
        }

        parent::__construct(NodeKind::TRAIT_PRECEDENCE_ADAPTATION);
    }

    public function originatingTrait(): QualifiedName
    {
        return $this->originatingTrait;
    }

    public function method(): Identifier
    {
        return $this->method;
    }

    /**
     * @return list<QualifiedName>
     */
    public function insteadOf(): array
    {
        return $this->insteadOf;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
