<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\FileNode;

final class PhpFile
{
    /**
     * @param list<PhpClass> $classes
     */
    public function __construct(
        private readonly FileHandle $handle,
        private readonly FileNode $fileNode,
        private readonly array $classes = []
    ) {
    }

    final public function handle(): FileHandle
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
