<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Index;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;

interface FileIndex
{
    public function find(?FileCriteria $criteria = null): FileCollection;

    public function reload(): void;
}
