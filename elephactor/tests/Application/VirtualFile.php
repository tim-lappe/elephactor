<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\AbsolutePath;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class VirtualFile implements File
{
    public function __construct(
        private VirtualDirectory $directory,
        private string $name,
        private string $content,
    ) {
        $this->directory->addFile($this);
    }

    public function absolutePath(): AbsolutePath
    {
        return new VirtualAbsolutePath($this->directory->absolutePath()->value() . '/' . $this->name);
    }

    public function content(): string
    {
        return $this->content;
    }

    public function equals(File $file): bool
    {
        return $this->name === $file->name();
    }

    public function moveTo(Directory $newDirectory): void
    {
        if (!$newDirectory instanceof VirtualDirectory) {
            throw new \InvalidArgumentException('New directory must be a virtual directory');
        }

        $this->directory->removeFile($this);

        $newDirectory->addFile($this);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function rename(string $newName): void
    {
        $this->name = $newName;
    }

    public function writeContent(string $content): void
    {
        $this->content = $content;
    }
}
