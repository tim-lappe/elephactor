<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\FullyQualifiedName;

final class AbsoluteClassIdentifier
{
    public function __construct(
        private readonly ClassIdentifier $classIdentifier,
        private readonly PhpNamespace $namespace,
    ) {
    }

    public function classIdentifier(): ClassIdentifier
    {
        return $this->classIdentifier;
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function fullIdentifier(): FullyQualifiedName
    {
        return new FullyQualifiedName([...$this->namespace->parts(), $this->classIdentifier->identifier()]);
    }
}
