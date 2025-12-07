<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Workspace;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\AbsolutePath;

final class FsAbsolutePath extends AbsolutePath
{
    public function __construct(string $value)
    {
        $realPath = realpath($value);
        if ($realPath === false) {
            throw new \InvalidArgumentException(sprintf('Path %s is not a valid absolute path', $value));
        }

        parent::__construct($realPath);
    }
}
