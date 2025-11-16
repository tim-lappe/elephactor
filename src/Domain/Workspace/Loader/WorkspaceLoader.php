<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Loader;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Workspace;

interface WorkspaceLoader
{
    public function load(Directory $workspaceDirectory): Workspace;
}
