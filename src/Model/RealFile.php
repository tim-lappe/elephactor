<?php

namespace TimLappe\Elephactor\Model;

final class RealFile
{
    public function __construct(
        private string $path,
    ) {
        if (!file_exists($path) || !is_file($path)) {
            throw new \InvalidArgumentException(sprintf('File %s does not exist', $path));
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDirectory(): RealDirectory
    {
        return new RealDirectory(dirname($this->path));
    }

    public function readContent(): string
    {
        $content = file_get_contents($this->path);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Could not read file %s', $this->path));
        }

        return $content;
    }

    public function writeContent(string $content): void
    {
        file_put_contents($this->path, $content);
    }

    public function rename(string $fileName): void
    {
        if (!rename($this->path, $this->getDirectory()->getPath() . '/' . $fileName)) {
            throw new \RuntimeException(sprintf('Could not rename file %s to %s', $this->path, $fileName));
        }
    }
}