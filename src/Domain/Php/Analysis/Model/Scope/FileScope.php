<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;

final class FileScope
{
    public function __construct(
        private readonly SemanticFileNode $fileNode,
    ) {
    }

    public function fileNode(): SemanticFileNode
    {
        return $this->fileNode;
    }
}
