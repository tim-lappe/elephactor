<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

final class SemanticClassDecleration extends SemanticClassLikeDecleration
{
    private ?SemanticClassExtends $extends = null;

    public function extends(): ?SemanticClassExtends
    {
        return $this->extends;
    }

    public function addExtends(SemanticClassExtends $extends): void
    {
        $this->extends = $extends;
    }

    public function children(): array
    {
        return [...parent::children(), ...($this->extends !== null ? [$this->extends] : [])];
    }

    public function __toString(): string
    {
        return 'Class: ' . $this->name()->__toString();
    }
}
