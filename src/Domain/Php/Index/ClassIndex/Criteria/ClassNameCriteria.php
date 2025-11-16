<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;

final class ClassNameCriteria implements PhpClassLikeCriteria
{
    public function __construct(
        private readonly FullyQualifiedName|QualifiedName|Identifier $identifier,
    ) {
    }

    public function matches(PhpClassLike $phpClassLike): bool
    {
        if ($this->identifier instanceof FullyQualifiedName) {
            return $phpClassLike->classLikeDeclaration()->name()->fullyQualifiedName()->equals($this->identifier);
        }

        if ($this->identifier instanceof QualifiedName) {
            return $phpClassLike->classLikeDeclaration()->name()->fullyQualifiedName()->endsWith($this->identifier);
        }

        return $phpClassLike->classLikeDeclaration()->name()->identifier()->equals($this->identifier);
    }
}
