<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Filesystem;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;

final class NativeFileHandle implements FileHandle
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
        if (file_put_contents($this->absolutePath, $content) === false) {
            throw new \RuntimeException(sprintf('Could not save content to file %s', $this->absolutePath));
        }
    }

    public function absolutePath(): string
    {
        return $this->absolutePath;
    }

    public function name(): string
    {
        return basename($this->absolutePath);
    }

    public function readContent(): string
    {
        $content = file_get_contents($this->absolutePath);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Could not read content of file %s', $this->absolutePath));
        }

        return $content;
    }
}
