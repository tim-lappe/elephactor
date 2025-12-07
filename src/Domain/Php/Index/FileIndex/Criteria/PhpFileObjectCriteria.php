<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

final class PhpFileObjectCriteria implements PhpFileCriteria
{
    public function __construct(
        private PhpFile $file,
    ) {
    }

    public function file(): PhpFile
    {
        return $this->file;
    }
}
