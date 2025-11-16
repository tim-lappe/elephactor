<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class NamespacedScope
{
    public function __construct(
        private readonly FileScope $parentScope,
        private readonly PhpNamespace $namespace,
    ) {
    }

    public function parentScope(): FileScope
    {
        return $this->parentScope;
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function resolveQualifiedName(QualifiedName $qualifiedName): FullyQualifiedName
    {
        if ($qualifiedName instanceof FullyQualifiedName) {
            return $qualifiedName;
        }

        $resolvedImport = $this->parentScope()->fileNode()->imports()->resolve($qualifiedName);
        if ($resolvedImport !== null) {
            return $resolvedImport;
        }

        if ($this->namespace()->isGlobal() === false) {
            return $this->namespace()->fullyQualifyName($qualifiedName);
        }

        return new FullyQualifiedName($qualifiedName->parts());
    }
}
