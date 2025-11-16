<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

abstract class AbstractSemanticNode implements SemanticNode
{
    /**
     * @var list<SemanticNode>
     */
    private array $astChildren = [];

    /**
     * @param list<SemanticNode> $children
     */
    final public function adoptAstChildren(array $children): void
    {
        $this->astChildren = $children;
    }

    /**
     * @return list<SemanticNode>
     */
    final protected function astChildren(): array
    {
        return $this->astChildren;
    }

    /**
     * @return list<SemanticNode>
     */
    protected function extraChildren(): array
    {
        return [];
    }

    public function children(): array
    {
        return [
            ...$this->astChildren(),
            ...$this->extraChildren(),
        ];
    }
}
