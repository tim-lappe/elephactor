<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

final class PropertyModifiers
{
    public function __construct(
        private readonly Visibility $visibility,
        private readonly bool $static = false,
        private readonly bool $readonly = false,
    ) {
    }

    public function visibility(): Visibility
    {
        return $this->visibility;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }
}
