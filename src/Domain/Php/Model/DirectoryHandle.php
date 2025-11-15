<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model;

interface DirectoryHandle
{
    public function rename(string $newName): void;
}
