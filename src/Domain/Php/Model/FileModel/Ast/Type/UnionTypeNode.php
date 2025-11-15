<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Type;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;

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
