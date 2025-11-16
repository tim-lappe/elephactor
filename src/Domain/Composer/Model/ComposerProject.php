<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Composer\Model;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;

final class ComposerProject
{
    public function __construct(
        private ComposerConfig $composerConfig,
        private Directory $workspaceDirectory,
    ) {
    }

    public function composerConfig(): ComposerConfig
    {
        return $this->composerConfig;
    }

    public function workspaceDirectory(): Directory
    {
        return $this->workspaceDirectory;
    }
}
