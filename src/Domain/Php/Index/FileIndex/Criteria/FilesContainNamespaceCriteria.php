<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Index\FileIndex\Criteria;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;

final class FilesContainNamespaceCriteria implements PhpFileCriteria
{
    public function __construct(
        private PhpNamespace $namespace,
        private bool $exactMatch = false,
    ) {
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function exactMatch(): bool
    {
        return $this->exactMatch;
    }
}
