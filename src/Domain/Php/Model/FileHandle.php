<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model;

interface FileHandle
{
    public function name(): string;

    public function rename(string $newName): void;

    public function readContent(): string;

    public function writeContent(string $content): void;
}
