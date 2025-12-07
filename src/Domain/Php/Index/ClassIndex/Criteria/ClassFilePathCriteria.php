<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\AbsolutePath;

final class ClassFilePathCriteria implements PhpClassLikeCriteria
{
    public function __construct(
        private readonly AbsolutePath $filePath,
    ) {
    }

    public function matches(PhpClassLike $phpClassLike): bool
    {
        return $phpClassLike->file()->handle()->absolutePath()->equals($this->filePath);
    }
}
