<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;

final class SemanticAstBuilder
{
    public function build(Node $node, SemanticNodeRegistry $registry): AbstractSemanticNode
    {
        $semanticNode = $registry->getOrCreate($node);

        $children = [];
        foreach ($node->children() as $child) {
            $children[] = $this->build($child, $registry);
        }

        $semanticNode->adoptAstChildren($children);

        return $semanticNode;
    }
}
