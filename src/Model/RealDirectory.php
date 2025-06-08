<?php

namespace TimLappe\Elephactor\Model;

final class RealDirectory
{
    public function __construct(
        private string $path,
    ) {
        $realPath = realpath($path);
        if ($realPath === false) {
            throw new \InvalidArgumentException(sprintf('Path %s is not a directory', $path));
        }
        
        $this->path = rtrim($realPath, '\\/');

        if (!is_dir($this->path)) {
            throw new \InvalidArgumentException(sprintf('Path %s is not a directory', $this->path));
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function __toString(): string
    {
        return $this->path;
    }
}