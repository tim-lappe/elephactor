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
        private FullyQualifiedName|QualifiedName|Identifier|string $identifier,
    ) {
        if (is_string($this->identifier)) {
            $className = trim($this->identifier);
            $className = trim($className, '\\');

            $this->identifier = QualifiedName::fromString($className);
        }
    }

    public function matches(PhpClassLike $phpClassLike): bool
    {
        if ($this->identifier instanceof FullyQualifiedName) {
            return $phpClassLike->fullyQualifiedName()->equals($this->identifier);
        }

        if ($this->identifier instanceof QualifiedName) {
            return $phpClassLike->fullyQualifiedName()->endsWith($this->identifier);
        }

        return $phpClassLike->fullyQualifiedName()->lastPart()->equals($this->identifier);
    }
}
