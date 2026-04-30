<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\ClassIndex;

use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLikeCollection;
use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\PhpClassLikeCriteria;

interface PhpClassLikeIndex
{
    public function find(?PhpClassLikeCriteria $criteria = null): PhpClassLikeCollection;

    public function reload(): void;
}
