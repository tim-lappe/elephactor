<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

interface Node
{
    /**
     * @return NodeCollection
     */
    public function children(): NodeCollection;
}
