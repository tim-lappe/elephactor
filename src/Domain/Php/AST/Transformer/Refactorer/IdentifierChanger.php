<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class IdentifierChanger implements Refactoring
{
    public function __construct(
        private readonly IdentifierNode $identifierNode,
        private readonly Identifier $newIdentifier,
    ) {
    }

    public function apply(): void
    {
        $this->identifierNode->replaceIdentifier($this->newIdentifier);
    }

    public function isApplicable(): bool
    {
        return !$this->identifierNode->identifier()->equals($this->newIdentifier);
    }
}
