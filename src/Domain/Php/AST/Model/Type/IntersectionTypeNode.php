<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final readonly class IntersectionTypeNode extends AbstractNode implements TypeNode
{
    /**
     * @param list<TypeNode> $types
     */
    public function __construct(
        private readonly array $types
    ) {
        if ($types === []) {
            throw new \InvalidArgumentException('Intersection type requires at least one referenced type');
        }

        parent::__construct();

        foreach ($types as $type) {
            $this->children()->add($type);
        }
    }

    /**
     * @return list<TypeNode>
     */
    public function types(): array
    {
        return $this->types;
    }
}
