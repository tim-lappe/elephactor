<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;

final class NamespaceFileMapItem
{
    public function __construct(
        private PhpNamespace $namespace,
        private FileCollection $file
    ) {
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function files(): FileCollection
    {
        return $this->file;
    }
}
