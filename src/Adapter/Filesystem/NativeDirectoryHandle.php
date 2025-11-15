<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Filesystem;

use TimLappe\Elephactor\Domain\Php\Model\DirectoryHandle;

final class NativeDirectoryHandle implements DirectoryHandle
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

    public function rename(string $newName): void
    {
        if (!rename($this->absolutePath, dirname($this->absolutePath) . '/' . $newName)) {
            throw new \RuntimeException(sprintf('Could not rename directory %s to %s', $this->absolutePath, dirname($this->absolutePath) . '/' . $newName));
        }

        $this->absolutePath = dirname($this->absolutePath) . '/' . $newName;
    }

    /**
     * @return array<NativeDirectoryHandle>
     */
    public function childDirectories(): array
    {
        $childDirectories = glob($this->absolutePath . '/*', GLOB_ONLYDIR);
        if ($childDirectories === false) {
            throw new \RuntimeException(sprintf('Could not get child directories of %s', $this->absolutePath));
        }

        return array_map(fn ($childDirectory) => new NativeDirectoryHandle($childDirectory), $childDirectories);
    }

    /**
     * @return array<NativeFileHandle>
     */
    public function childFiles(): array
    {
        $childFiles = glob($this->absolutePath . '/*.php');
        if ($childFiles === false) {
            throw new \RuntimeException(sprintf('Could not get child files of %s', $this->absolutePath));
        }

        return array_map(fn ($childFile) => new NativeFileHandle($childFile), $childFiles);
    }

    public function name(): string
    {
        return basename($this->absolutePath);
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }
}
