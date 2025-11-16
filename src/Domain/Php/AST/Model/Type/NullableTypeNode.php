<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class NullableTypeNode extends AbstractNode implements TypeNode
{
    public function __construct(
        private readonly TypeNode $inner
    ) {
        parent::__construct(NodeKind::TYPE_REFERENCE);
    }

    public function inner(): TypeNode
    {
        return $this->inner;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->inner];
    }
}
