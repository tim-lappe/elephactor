<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\FileNode;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class PhpFile
{
    /**
     * @param list<PhpClass> $classes
     */
    public function __construct(
        private readonly File $handle,
        private readonly FileNode $fileNode,
        private readonly array $classes = []
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

    /**
     * @return list<PhpClass>
     */
    final public function classes(): array
    {
        return $this->classes;
    }
}
