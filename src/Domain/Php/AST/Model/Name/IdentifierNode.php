<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Name;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class IdentifierNode extends AbstractNode
{
    public function __construct(
        private Identifier $identifier,
        private readonly Node $owner,
    ) {
        parent::__construct(NodeKind::IDENTIFIER);
    }

    public function owner(): Node
    {
        return $this->owner;
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    public function changeIdentifier(Identifier $identifier): void
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

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
