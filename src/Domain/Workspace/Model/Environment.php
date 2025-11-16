<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model;

use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;

final class Environment
{
    public function __construct(
        private PhpVersion $phpVersion,
    ) {
    }

    public function phpVersion(): PhpVersion
    {
        return $this->phpVersion;
    }
}
