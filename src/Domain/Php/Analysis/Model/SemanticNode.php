<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

interface SemanticNode
{
    /**
     * @return list<SemanticNode>
     */
    public function children(): array;

    public function __toString(): string;
}
