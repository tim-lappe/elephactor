<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

final class UsageMapItem extends AbstractSemanticNode
{
    public function __construct(
        private readonly FullyQualifiedName $usedName,
        private readonly SemanticNode $referencedNode,
    ) {
    }

    public function usedName(): FullyQualifiedName
    {
        return $this->usedName;
    }

    public function referencedNode(): SemanticNode
    {
        return $this->referencedNode;
    }

    public function children(): array
    {
        return [$this->referencedNode];
    }

    public function __toString(): string
    {
        return 'UsageMapItem: ' . $this->usedName()->__toString();
    }
}
