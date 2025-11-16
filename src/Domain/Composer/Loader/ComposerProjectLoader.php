<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Composer\Loader;

use TimLappe\Elephactor\Domain\Composer\Model\ComposerProject;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

interface ComposerProjectLoader
{
    public function supports(Directory $workspaceDirectory): bool;

    public function load(Directory $composerJsonDirectory): ComposerProject;
}
