<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class UnionTypeNode extends AbstractNode implements TypeNode
{
    /**
     * @param list<TypeNode> $types
     */
    public function __construct(
        array $types
    ) {
        if ($types === []) {
            throw new \InvalidArgumentException('Union type requires at least one referenced type');
        }

        parent::__construct();

        foreach ($types as $type) {
            $this->children()->add('type', $type);
        }
    }

    /**
     * @return list<TypeNode>
     */
    public function types(): array
    {
        return $this->children()->getAllOf('type', TypeNode::class);
    }
}
