<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Transformation\Refactorer;

interface Refactoring
{
    public function apply(): void;
}