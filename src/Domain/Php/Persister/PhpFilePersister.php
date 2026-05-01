<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Persister;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

interface PhpFilePersister
{
    public function persist(PhpFile $phpFile): void;
}
