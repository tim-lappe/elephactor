<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

final class DocBlock
{
    private readonly string $content;

    public function __construct(string $content)
    {
        $normalized = trim($content);

        if ($normalized === '') {
            throw new \InvalidArgumentException('DocBlock cannot be empty');
        }

        $this->content = $normalized;
    }

    public function content(): string
    {
        return $this->content;
    }
}
