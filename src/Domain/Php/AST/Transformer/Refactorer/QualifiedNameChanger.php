<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class QualifiedNameChanger implements Refactoring
{
    public function __construct(
        private readonly QualifiedNameNode $qualifiedName,
        private readonly QualifiedName $newQualifiedName,
    ) {
    }

    public function apply(): void
    {
        $this->qualifiedName->replaceQualifiedName($this->newQualifiedName);
    }

    public function isApplicable(): bool
    {
        return !$this->qualifiedName->qualifiedName()->equals($this->newQualifiedName);
    }
}
