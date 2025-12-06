<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Traversal;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Visitor\NodeVisitor;

final class NodeTraverser
{
    /**
     * @param list<NodeVisitor> $nodeVisitors
     */
    public function __construct(
        private readonly array $nodeVisitors,
    ) {
    }

    public function traverse(Node $node): void
    {
        foreach ($this->nodeVisitors as $nodeVisitor) {
            $nodeVisitor->enter($node);
        }
        
        foreach ($node->children()->toArray() as $child) {
            $this->traverse($child);
        }

        foreach ($this->nodeVisitors as $nodeVisitor) {
            $nodeVisitor->leave($node);
        }
    }
}