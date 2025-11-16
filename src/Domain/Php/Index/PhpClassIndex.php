<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;

interface PhpClassIndex
{
    public function find(?PhpClassCriteria $criteria = null): PhpClassCollection;

    public function reload(): void;
}
