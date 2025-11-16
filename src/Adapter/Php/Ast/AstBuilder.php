<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast;

use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;

interface AstBuilder
{
    public function build(string $content): FileNode;
}
