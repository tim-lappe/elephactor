<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class PropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    private IdentifierNode|ExpressionNode $property;

    public function __construct(
        private readonly ExpressionNode $object,
        Identifier|ExpressionNode $property,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct(NodeKind::PROPERTY_FETCH_EXPRESSION);

        $this->property = $property instanceof Identifier ? new IdentifierNode($property, $this) : $property;
    }

    public function object(): ExpressionNode
    {
        return $this->object;
    }

    public function property(): IdentifierNode|ExpressionNode
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

        $children[] = $this->property;

        return $children;
    }
}
