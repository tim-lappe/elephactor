<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Workspace\Model\Filesystem;

interface File
{
    public function name(): string;

    public function content(): string;

    public function equals(File $file): bool;

    public function rename(string $newName): void;

    public function writeContent(string $content): void;

    public function moveTo(Directory $newDirectory): void;
}
