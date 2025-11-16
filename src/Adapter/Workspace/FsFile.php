<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Workspace;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class FsFile implements File
{
    public function __construct(
        private string $absolutePath,
    ) {
        $realPath = realpath($absolutePath);
        if ($realPath === false) {
            throw new \InvalidArgumentException(sprintf('File %s does not exist', $absolutePath));
        }

        $this->absolutePath = $realPath;
    }

    public function content(): string
    {
        $content = file_get_contents($this->absolutePath);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Could not read file %s', $this->absolutePath));
        }

        return $content;
    }

    public function name(): string
    {
        return basename($this->absolutePath);
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }

    public function directory(): FsDirectory
    {
        return new FsDirectory(dirname($this->absolutePath));
    }

    public function rename(string $newName): void
    {
        if (file_exists(dirname($this->absolutePath) . '/' . $newName)) {
            throw new \RuntimeException(sprintf('File %s already exists', dirname($this->absolutePath) . '/' . $newName));
        }

        if (!rename($this->absolutePath, dirname($this->absolutePath) . '/' . $newName)) {
            throw new \RuntimeException(sprintf('Could not rename file %s to %s', $this->absolutePath, dirname($this->absolutePath) . '/' . $newName));
        }

        $this->absolutePath = dirname($this->absolutePath) . '/' . $newName;
    }

    public function writeContent(string $content): void
    {
        file_put_contents($this->absolutePath, $content);
    }

    public function moveTo(Directory $newDirectory): void
    {
        if (!$newDirectory instanceof FsDirectory) {
            throw new \InvalidArgumentException(sprintf('Directory %s is not a filesystem directory', get_class($newDirectory)));
        }

        $newPath = $newDirectory->absolutePath() . '/' . $this->name();
        if (!rename($this->absolutePath, $newPath)) {
            throw new \RuntimeException(sprintf('Could not move file %s to %s', $this->absolutePath, $newPath));
        }

        $this->absolutePath = $newPath;
    }

    public function equals(File $file): bool
    {
        if (!$file instanceof FsFile) {
            return false;
        }

        return $this->absolutePath() === $file->absolutePath();
    }
}
