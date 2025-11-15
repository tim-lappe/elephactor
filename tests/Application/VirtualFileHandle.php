<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;

final class VirtualFileHandle implements FileHandle
{
    public function __construct(
        private string $name,
        private string $content,
    ) {
    }

    public function name(): string 
    { 
        return $this->name; 
    }

    public function rename(string $newName): void 
    {
        $this->name = $newName;
    }

    public function readContent(): string 
    { 
        return $this->content;
    }

    public function writeContent(string $content): void 
    { 
        $this->content = $content;
    }
}