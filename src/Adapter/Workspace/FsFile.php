<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Workspace;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class FsFile implements File
{
    public function __construct(
        private FsAbsolutePath $absolutePath,
    ) {
    }

    public function content(): string
    {
        $content = file_get_contents($this->absolutePath->value());
        if ($content === false) {
            throw new \RuntimeException(sprintf('Could not read file %s', $this->absolutePath->value()));
        }

        return $content;
    }

    public function name(): string
    {
        return basename($this->absolutePath->value());
    }

    public function absolutePath(): FsAbsolutePath
    {
        return $this->absolutePath;
    }

    public function directory(): FsDirectory
    {
        return new FsDirectory(new FsAbsolutePath(dirname($this->absolutePath->value())));
    }

    public function rename(string $newName): void
    {
        if (file_exists(dirname($this->absolutePath->value()) . '/' . $newName)) {
            throw new \RuntimeException(sprintf('File %s already exists', dirname($this->absolutePath->value()) . '/' . $newName));
        }

        if (!rename($this->absolutePath->value(), dirname($this->absolutePath->value()) . '/' . $newName)) {
            throw new \RuntimeException(sprintf('Could not rename file %s to %s', $this->absolutePath->value(), dirname($this->absolutePath->value()) . '/' . $newName));
        }

        $this->absolutePath = new FsAbsolutePath(dirname($this->absolutePath->value()) . '/' . $newName);
    }

    public function writeContent(string $content): void
    {
        file_put_contents($this->absolutePath->value(), $content);
    }

    public function moveTo(Directory $newDirectory): void
    {
        if (!$newDirectory instanceof FsDirectory) {
            throw new \InvalidArgumentException(sprintf('Directory %s is not a filesystem directory', get_class($newDirectory)));
        }

        $newPath = $newDirectory->absolutePath() . '/' . $this->name();
        if (!rename($this->absolutePath->value(), $newPath)) {
            throw new \RuntimeException(sprintf('Could not move file %s to %s', $this->absolutePath->value(), $newPath));
        }

        $this->absolutePath = new FsAbsolutePath($newPath);
    }

    public function equals(File $file): bool
    {
        if (!$file instanceof FsFile) {
            return false;
        }

        return $this->absolutePath()->equals($file->absolutePath());
    }
}
