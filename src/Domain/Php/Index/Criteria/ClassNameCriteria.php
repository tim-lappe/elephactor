<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\Criteria;

use TimLappe\Elephactor\Domain\Php\Index\PhpClassCriteria;

final class ClassNameCriteria implements PhpClassCriteria
{
    public function __construct(
        private readonly string $className,
    ) {
    }

    public function className(): string
    {
        return $this->className;
    }
}
