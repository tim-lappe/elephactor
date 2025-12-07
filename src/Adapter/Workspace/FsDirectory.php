<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Workspace;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\AbsolutePath;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\DirectoryCollection;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;

final class FsDirectory implements Directory
{
    public function __construct(
        private FsAbsolutePath $absolutePath,
    ) {
    }

    public function absolutePath(): FsAbsolutePath
    {
        return $this->absolutePath;
    }

    public function equals(Directory $directory): bool
    {
        if (!$directory instanceof FsDirectory) {
            return false;
        }

        return $this->absolutePath()->equals($directory->absolutePath());
    }

    public function contains(Directory|File $item): bool
    {
        if ($item instanceof FsFile) {
            return $item->absolutePath()->startsWith($this->absolutePath());
        }

        if ($item instanceof FsDirectory) {
            return $item->absolutePath()->startsWith($this->absolutePath());
        }

        throw new \InvalidArgumentException(sprintf('Item %s is not a filesystem directory or file', get_class($item)));
    }

    public function name(): string
    {
        return basename($this->absolutePath->value());
    }

    public function childDirectories(): DirectoryCollection
    {
        $childDirectories = glob($this->absolutePath->value() . '/*', GLOB_ONLYDIR);
        if ($childDirectories === false) {
            throw new \RuntimeException(sprintf('Could not get child directories of %s', $this->absolutePath->value()));
        }

        return new DirectoryCollection(array_map(fn ($childDirectory) => new FsDirectory(new FsAbsolutePath($childDirectory)), $childDirectories));
    }

    public function childFiles(): FileCollection
    {
        $childFiles = glob($this->absolutePath->value() . '/*.*');
        if ($childFiles === false) {
            throw new \RuntimeException(sprintf('Could not get child files of %s', $this->absolutePath->value()));
        }

        return new FileCollection(array_map(fn ($childFile) => new FsFile(new FsAbsolutePath($childFile)), $childFiles));
    }

    /**
     * @inheritDoc
     */
    public function find(AbsolutePath $path): null|File|Directory
    {
        if (!$path instanceof FsAbsolutePath) {
            return null;
        }

        if ($this->absolutePath->equals($path)) {
            return $this;
        }

        if ($path->startsWith($this->absolutePath)) {
            foreach ($this->childFiles()->toArray() as $childFile) {
                if ($childFile->absolutePath()->equals($path)) {
                    return $childFile;
                }
            }

            foreach ($this->childDirectories()->toArray() as $childDirectory) {
                $found = $childDirectory->find($path);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    public function parent(): ?Directory
    {
        if (dirname($this->absolutePath->value()) === $this->absolutePath->value() || dirname($this->absolutePath->value()) === '.') {
            return null;
        }

        return new FsDirectory(new FsAbsolutePath(dirname($this->absolutePath->value())));
    }
}
