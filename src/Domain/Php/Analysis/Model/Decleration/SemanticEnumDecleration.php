<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

final class SemanticEnumDecleration extends SemanticClassLikeDecleration
{
    public function __toString(): string
    {
        return 'Enum: ' . $this->name()->__toString();
    }
}
