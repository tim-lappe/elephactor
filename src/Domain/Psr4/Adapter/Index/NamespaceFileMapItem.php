<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFileCollection;

final class NamespaceFileMapItem
{
    public function __construct(
        private QualifiedName $namespace,
        private PhpFileCollection $file
    ) {
    }

    public function namespace(): QualifiedName
    {
        return $this->namespace;
    }

    public function files(): PhpFileCollection
    {
        return $this->file;
    }
}
