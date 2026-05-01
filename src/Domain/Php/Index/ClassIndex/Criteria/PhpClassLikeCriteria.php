<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;

interface PhpClassLikeCriteria
{
    public function matches(PhpClassLike $phpClassLike): bool;
}
