<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

interface Node
{
    public function kind(): NodeKind;

    /**
     * @return list<Node>
     */
    public function children(): array;
}
