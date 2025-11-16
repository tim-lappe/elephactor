<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

final class SemanticInterfaceDecleration extends SemanticClassLikeDecleration
{
    /**
     * @var list<SemanticInterfaceExtends>
     */
    private array $extends = [];

    public function addExtends(SemanticInterfaceExtends $extends): void
    {
        $this->extends[] = $extends;
    }

    /**
     * @return list<SemanticInterfaceExtends>
     */
    public function extends(): array
    {
        return $this->extends;
    }

    public function children(): array
    {
        return [
            ...parent::children(),
            ...$this->extends,
        ];
    }

    public function __toString(): string
    {
        return 'Interface: ' . $this->name()->__toString();
    }
}
