<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;

final class SemanticGenericNode extends AbstractSemanticNode
{
    public function __construct(
        private readonly Node $node,
    ) {
    }

    public function astNode(): Node
    {
        return $this->node;
    }

    public function __toString(): string
    {
        return 'GenericNode: ' . $this->node::class;
    }
}
