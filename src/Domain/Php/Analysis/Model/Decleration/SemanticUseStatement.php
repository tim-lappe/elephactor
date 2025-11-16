<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticQualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Name\SemanticIdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

final class SemanticUseStatement extends AbstractSemanticNode
{
    public function __construct(
        private readonly SemanticQualifiedNameNode $name,
        private readonly ?SemanticIdentifierNode $alias = null,
        private readonly ?SemanticQualifiedNameNode $groupPrefix = null
    ) {
    }

    public function children(): array
    {
        return array_values(array_filter([
            ...parent::children(),
            $this->name,
            $this->alias,
            $this->groupPrefix,
        ], fn ($child) => $child !== null));
    }

    public function groupPrefix(): ?SemanticQualifiedNameNode
    {
        return $this->groupPrefix;
    }

    public function __toString(): string
    {
        return 'ImportItem: ' . $this->name()->__toString() . ' ' . ($this->alias !== null ? 'as ' . $this->alias->identifier()->__toString() : '');
    }

    public function name(): SemanticQualifiedNameNode
    {
        return $this->name;
    }

    public function fullyQualifiedName(): FullyQualifiedName
    {
        $parts = [];
        if ($this->groupPrefix !== null) {
            $parts = [...$parts, ...$this->groupPrefix->qualifiedName()->parts()];
        }

        $parts = [...$parts, ...$this->name()->qualifiedName()->parts()];

        return new FullyQualifiedName($parts);
    }

    public function alias(): ?SemanticIdentifierNode
    {
        return $this->alias;
    }
}
