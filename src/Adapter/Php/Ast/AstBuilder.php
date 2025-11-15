<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\FileNode;

interface AstBuilder
{
    public function build(string $content): FileNode;
}
