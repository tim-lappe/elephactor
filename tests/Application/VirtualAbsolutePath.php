<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\AbsolutePath;

final class VirtualAbsolutePath extends AbsolutePath
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}
