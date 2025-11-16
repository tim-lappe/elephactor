<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model\Filesystem;

interface Directory
{
    public function name(): string;

    public function childFiles(): FileCollection;

    public function childDirectories(): DirectoryCollection;

    public function contains(Directory|File $item): bool;

    public function equals(Directory $directory): bool;

    public function parent(): ?Directory;
}
