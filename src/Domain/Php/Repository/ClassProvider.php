<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Repository;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;

interface ClassProvider
{
    public function provide(): PhpClassCollection;
}
