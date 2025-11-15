<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\ClassIdentifier;

interface Psr4ClassHandler
{
    public function rename(ClassIdentifier $newClassIdentifier): void;
}
