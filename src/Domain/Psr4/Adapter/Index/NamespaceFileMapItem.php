<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFileCollection;

final class NamespaceFileMapItem
{
    public function __construct(
        private PhpNamespace $namespace,
        private PhpFileCollection $file
    ) {
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function files(): PhpFileCollection
    {
        return $this->file;
    }
}
