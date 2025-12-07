<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer;

interface Refactoring
{
    public function apply(): void;

    public function isApplicable(): bool;
}
