<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

final class Psr4AutoloadMapItem
{
    public function __construct(
        private PhpNamespace $namespace,
        private Directory $directory,
    ) {
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function directory(): Directory
    {
        return $this->directory;
    }
}
