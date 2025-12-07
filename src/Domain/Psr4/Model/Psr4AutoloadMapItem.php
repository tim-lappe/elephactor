<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

final class Psr4AutoloadMapItem
{
    public function __construct(
        private QualifiedName $namespace,
        private Directory $directory,
    ) {
    }

    public function namespace(): QualifiedName
    {
        return $this->namespace;
    }

    public function directory(): Directory
    {
        return $this->directory;
    }
}
