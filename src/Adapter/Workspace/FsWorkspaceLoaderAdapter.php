<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Workspace;

use TimLappe\Elephactor\Domain\Workspace\Loader\WorkspaceLoader;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Workspace;
use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;
use TimLappe\Elephactor\Domain\Workspace\Model\Environment;

final class FsWorkspaceLoaderAdapter implements WorkspaceLoader
{
    public function load(Directory $workspaceDirectory): Workspace
    {
        if (!$workspaceDirectory instanceof FsDirectory) {
            throw new \InvalidArgumentException(sprintf('Workspace directory %s is not a FilesystemDirectory', $workspaceDirectory->name()));
        }

        return new Workspace($workspaceDirectory, new Environment(PhpVersion::fromHost()));
    }
}
