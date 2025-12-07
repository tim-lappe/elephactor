<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class FilesContainNamespaceCriteria implements PhpFileCriteria
{
    public function __construct(
        private QualifiedName $namespace,
        private bool $exactMatch = false,
    ) {
    }

    public function namespace(): QualifiedName
    {
        return $this->namespace;
    }

    public function exactMatch(): bool
    {
        return $this->exactMatch;
    }
}
