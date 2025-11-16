<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Composer;

use TimLappe\Elephactor\Adapter\Workspace\FsDirectory;
use TimLappe\Elephactor\Adapter\Workspace\FsFile;
use TimLappe\Elephactor\Domain\Composer\Loader\ComposerProjectLoader;
use TimLappe\Elephactor\Domain\Composer\Model\ComposerProject;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class FsComposerProjectLoaderAdapter implements ComposerProjectLoader
{
    public function __construct(
        private readonly ComposerConfigJsonLoader $composerConfigJsonLoader,
    ) {
    }

    public function supports(Directory $workspaceDirectory): bool
    {
        if (!$workspaceDirectory instanceof FsDirectory) {
            return false;
        }

        return $workspaceDirectory->childFiles()->first(fn (File $file) => $file->name() === 'composer.json') !== null;
    }

    public function load(Directory $workspaceDirectory): ComposerProject
    {
        if (!$workspaceDirectory instanceof FsDirectory) {
            throw new \InvalidArgumentException(sprintf('Workspace directory %s is not a FilesystemDirectory', $workspaceDirectory->name()));
        }

        $composerJsonFile = $workspaceDirectory
            ->childFiles()
            ->first(fn (File $file) => $file->name() === 'composer.json');

        if ($composerJsonFile === null) {
            throw new \RuntimeException(sprintf('Could not find composer.json in %s', $workspaceDirectory->name()));
        }

        if (!$composerJsonFile instanceof FsFile) {
            throw new \InvalidArgumentException(sprintf('Composer.json file %s is not a FilesystemFile', $composerJsonFile->name()));
        }

        $composerConfig = $this->composerConfigJsonLoader->load($composerJsonFile);

        return new ComposerProject($composerConfig, $workspaceDirectory);
    }
}
