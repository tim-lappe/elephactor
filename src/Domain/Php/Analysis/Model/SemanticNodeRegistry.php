<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;

final class SemanticNodeRegistry
{
    /**
     * @var array<int, AbstractSemanticNode>
     */
    private array $nodes = [];

    public function register(Node $node, AbstractSemanticNode $semanticNode): void
    {
        $this->nodes[spl_object_id($node)] = $semanticNode;
    }

    public function find(Node $node): ?AbstractSemanticNode
    {
        return $this->nodes[spl_object_id($node)] ?? null;
    }

    public function getOrCreate(Node $node): AbstractSemanticNode
    {
        $key = spl_object_id($node);
        if (!isset($this->nodes[$key])) {
            $this->nodes[$key] = new SemanticGenericNode($node);
        }

        return $this->nodes[$key];
    }
}
