<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast;

interface Node
{
    public function kind(): NodeKind;

    /**
     * @return list<Node>
     */
    public function children(): array;
}
