<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Type;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\LiteralValue;

final class ArrayShapeField
{
    public function __construct(
        private readonly TypeNode $type,
        private readonly Identifier|LiteralValue|null $key = null,
        private readonly bool $optional = false,
        private readonly bool $variadic = false,
    ) {
        if ($variadic && $key !== null) {
            throw new \InvalidArgumentException('Variadic array shape field cannot declare a key');
        }
    }

    public function type(): TypeNode
    {
        return $this->type;
    }

    public function key(): Identifier|LiteralValue|null
    {
        return $this->key;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function isVariadic(): bool
    {
        return $this->variadic;
    }
}
