<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

use TimLappe\Elephactor\Domain\Php\AST\Visitor\NodeVisitor;

interface Node
{
    /**
     * @return NodeCollection
     */
    public function children(): NodeCollection;
}
