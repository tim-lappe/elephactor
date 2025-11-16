<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class CallableParameter
{
    public function __construct(
        private readonly ?TypeNode $type,
        private readonly bool $byReference = false,
        private readonly bool $variadic = false,
    ) {
    }

    public function type(): ?TypeNode
    {
        return $this->type;
    }

    public function isByReference(): bool
    {
        return $this->byReference;
    }

    public function isVariadic(): bool
    {
        return $this->variadic;
    }
}
