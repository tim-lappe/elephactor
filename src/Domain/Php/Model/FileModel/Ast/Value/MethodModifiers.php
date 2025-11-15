<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

final class MethodModifiers
{
    public function __construct(
        private readonly Visibility $visibility,
        private readonly bool $static = false,
        private readonly bool $abstract = false,
        private readonly bool $final = false,
    ) {
        if ($this->abstract && $this->final) {
            throw new \InvalidArgumentException('Method cannot be both abstract and final');
        }
    }

    public function visibility(): Visibility
    {
        return $this->visibility;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }
}
