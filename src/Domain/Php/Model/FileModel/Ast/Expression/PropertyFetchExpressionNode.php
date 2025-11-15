<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class PropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $object,
        private readonly Identifier|ExpressionNode $property,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct(NodeKind::PROPERTY_FETCH_EXPRESSION);
    }

    public function object(): ExpressionNode
    {
        return $this->object;
    }

    public function property(): Identifier|ExpressionNode
    {
        return $this->property;
    }

    public function isNullsafe(): bool
    {
        return $this->nullsafe;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->object];

        if ($this->property instanceof ExpressionNode) {
            $children[] = $this->property;
        }

        return $children;
    }
}
