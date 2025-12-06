<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class PhpFile
{
    public function __construct(
        private readonly File $handle,
        private readonly FileNode $fileNode,
    ) {
    }

    final public function handle(): File
    {
        return $this->handle;
    }

    final public function fileNode(): FileNode
    {
        return $this->fileNode;
    }
}
