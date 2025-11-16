<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

final class ClassModifiers
{
    public function __construct(
        private readonly bool $abstract = false,
        private readonly bool $final = false,
        private readonly bool $readonly = false,
    ) {
        if ($this->abstract && $this->final) {
            throw new \InvalidArgumentException('Class cannot be both abstract and final');
        }
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }
}
