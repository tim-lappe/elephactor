<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class PhpFile
{
    public function __construct(
        private readonly File $handle,
        private readonly SemanticFileNode $fileNode,
    ) {
    }

    final public function handle(): File
    {
        return $this->handle;
    }

    final public function fileNode(): SemanticFileNode
    {
        return $this->fileNode;
    }
}
