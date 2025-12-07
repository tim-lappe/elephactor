<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\FileCollection;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\DirectoryCollection;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\AbsolutePath;

use function get_class;

final class VirtualDirectory implements Directory
{
    private FileCollection $childFiles;
    private DirectoryCollection $childDirectories;

    public function __construct(
        private string $name,
        private ?VirtualDirectory $parent = null,
    ) {
        if (str_contains($name, '/')) {
            throw new \InvalidArgumentException(sprintf('Name %s must not contain slashes. Create directories using createOrGetDirecotry() instead.', $name));
        }

        $this->name = trim($name, '/');
        $this->childFiles = new FileCollection([]);
        $this->childDirectories = new DirectoryCollection([]);
    }

    public function addFile(File $file): void
    {
        $this->childFiles->add($file);
    }

    public function removeFile(File $file): void
    {
        $this->childFiles->remove($file);
    }

    public function createFile(string $name, string $content): VirtualFile
    {
        $file = new VirtualFile($this, $name, $content);
        $this->addFile($file);

        return $file;
    }

    public function createOrGetDirecotry(string $name): VirtualDirectory
    {
        $existingDirectory = $this->childDirectories->find($name);
        if ($existingDirectory instanceof VirtualDirectory) {
            return $existingDirectory;
        }

        $newDir = new VirtualDirectory($name, $this);
        $this->childDirectories->add($newDir);

        return $newDir;
    }

    public function name(): string
    {
        return basename($this->name);
    }

    public function childFiles(): FileCollection
    {
        return $this->childFiles;
    }

    public function childDirectories(): DirectoryCollection
    {
        return $this->childDirectories;
    }

    public function contains(Directory|File $item): bool
    {
        if ($item instanceof VirtualFile) {
            return $this->childFiles->first(fn (VirtualFile $file): bool => $file->equals($item)) !== null;
        }

        if ($item instanceof VirtualDirectory) {
            return $this->childDirectories->first(fn (VirtualDirectory $directory): bool => $directory->equals($item)) !== null;
        }

        throw new \InvalidArgumentException(sprintf('Item %s is not a virtual file or directory', get_class($item)));
    }

    public function equals(Directory $directory): bool
    {
        if (!$directory instanceof VirtualDirectory) {
            return false;
        }

        return $this->absolutePath()->equals($directory->absolutePath());
    }

    /**
     * @inheritDoc
     */
    public function parent(): ?Directory
    {
        return $this->parent;
    }

    public function find(AbsolutePath $path): null|File|Directory
    {
        if ($this->absolutePath()->equals($path)) {
            return $this;
        }

        return $this->parent?->find($path);
    }

    public function absolutePath(): AbsolutePath
    {
        if ($this->parent === null) {
            return new VirtualAbsolutePath($this->name());
        }

        return new VirtualAbsolutePath($this->parent->absolutePath()->value() . '/' . $this->name());
    }
}
