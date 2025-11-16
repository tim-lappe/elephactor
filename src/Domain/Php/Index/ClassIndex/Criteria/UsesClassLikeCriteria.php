<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;

final class UsesClassLikeCriteria implements PhpClassLikeCriteria
{
    public function __construct(
        private readonly PhpClassLike $usedClassLike,
    ) {
    }

    public function matches(PhpClassLike $checkClassLike): bool
    {
        $usages = $checkClassLike->classLikeDeclaration()->usages();
        $usage = $usages->get($this->usedClassLike->classLikeDeclaration()->name()->fullyQualifiedName());

        return count($usage) > 0;
    }
}
