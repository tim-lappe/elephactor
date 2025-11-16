<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class SpecialTypeNode extends AbstractNode implements TypeNode
{
    public function __construct(
        private readonly SpecialType $type
    ) {
        parent::__construct(NodeKind::TYPE_REFERENCE);
    }

    public function type(): SpecialType
    {
        return $this->type;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
