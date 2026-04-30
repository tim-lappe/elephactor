<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class NullableTypeNode extends AbstractNode implements TypeNode
{
    public function __construct(
        TypeNode $inner
    ) {
        parent::__construct();
        $this->children()->add('inner', $inner);
    }

    public function inner(): TypeNode
    {
        return $this->children()->getOne('inner', TypeNode::class) ?? throw new \RuntimeException('Inner type not found');
    }
}
