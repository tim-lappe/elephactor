<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Name;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

class IdentifierNode extends AbstractNode
{
    public function __construct(
        private Identifier $identifier,
    ) {
        parent::__construct();
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    public function replaceIdentifier(Identifier $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function value(): string
    {
        return $this->identifier->value();
    }

    public function equals(string|Identifier $other): bool
    {
        return $this->identifier->equals($other);
    }

    public function __toString(): string
    {
        return $this->identifier->__toString();
    }
}
