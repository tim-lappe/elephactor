<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Workspace;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\DirectoryCollection;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;

final class FsDirectory implements Directory
{
    public function __construct(
        private string $absolutePath,
    ) {
        $realPath = realpath($absolutePath);
        if ($realPath === false) {
            throw new \InvalidArgumentException(sprintf('Directory %s does not exist', $absolutePath));
        }

        $this->absolutePath = $realPath;
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }

    public function equals(Directory $directory): bool
    {
        if (!$directory instanceof FsDirectory) {
            return false;
        }

        return $this->absolutePath() === $directory->absolutePath();
    }

    public function contains(Directory|File $item): bool
    {
        if ($item instanceof FsFile) {
            return str_starts_with($item->absolutePath(), $this->absolutePath);
        }

        if ($item instanceof FsDirectory) {
            return str_starts_with($item->absolutePath(), $this->absolutePath);
        }

        throw new \InvalidArgumentException(sprintf('Item %s is not a filesystem directory or file', get_class($item)));
    }

    public function name(): string
    {
        return basename($this->absolutePath);
    }

    public function childDirectories(): DirectoryCollection
    {
        $childDirectories = glob($this->absolutePath . '/*', GLOB_ONLYDIR);
        if ($childDirectories === false) {
            throw new \RuntimeException(sprintf('Could not get child directories of %s', $this->absolutePath));
        }

        return new DirectoryCollection(array_map(fn ($childDirectory) => new FsDirectory($childDirectory), $childDirectories));
    }

    public function childFiles(): FileCollection
    {
        $childFiles = glob($this->absolutePath . '/*.*');
        if ($childFiles === false) {
            throw new \RuntimeException(sprintf('Could not get child files of %s', $this->absolutePath));
        }

        return new FileCollection(array_map(fn ($childFile) => new FsFile($childFile), $childFiles));
    }

    /**
     * @inheritDoc
     */
    public function parent(): ?Directory
    {
        if (dirname($this->absolutePath) === $this->absolutePath || dirname($this->absolutePath) === '.') {
            return null;
        }

        return new FsDirectory(dirname($this->absolutePath));
    }
}
