<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Element;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\ClassIdentifier;

final class PhpClassReference
{
    public function __construct(
        private readonly ClassIdentifier $classIdentifier,
    ) {
    }

    public function classIdentifier(): ClassIdentifier
    {
        return $this->classIdentifier;
    }
}
