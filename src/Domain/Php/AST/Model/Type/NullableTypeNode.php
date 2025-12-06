<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final readonly class NullableTypeNode extends AbstractNode implements TypeNode
{
    public function __construct(
        private readonly TypeNode $inner
    ) {
        parent::__construct();
        $this->children()->add($this->inner);
    }

    public function inner(): TypeNode
    {
        return $this->inner;
    }
}
