<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class UnionTypeNode extends AbstractNode implements TypeNode
{
    /**
     * @param list<TypeNode> $types
     */
    public function __construct(
        private readonly array $types
    ) {
        if ($types === []) {
            throw new \InvalidArgumentException('Union type requires at least one referenced type');
        }

        parent::__construct(NodeKind::TYPE_REFERENCE);
    }

    /**
     * @return list<TypeNode>
     */
    public function types(): array
    {
        return $this->types;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->types;
    }
}
