<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\FileIndex;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFileCollection;
use TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria\PhpFileCriteria;

interface PhpFileIndex
{
    public function find(?PhpFileCriteria $criteria = null): PhpFileCollection;

    public function reload(): void;
}
