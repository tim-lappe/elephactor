<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Workspace\Index\FileCriteria;

final class FilesInNamespaceCriteria implements FileCriteria
{
    public function __construct(
        private PhpNamespace $namespace,
        private bool $exactMatch = false,
    ) {
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function exactMatch(): bool
    {
        return $this->exactMatch;
    }
}
