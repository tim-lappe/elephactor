<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final readonly class PropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    private IdentifierNode|ExpressionNode $property;

    public function __construct(
        private readonly ExpressionNode $object,
        Identifier|ExpressionNode $property,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct();

        $this->property = $property instanceof Identifier ? new IdentifierNode($property) : $property;

        $this->children()->add($this->object);
        $this->children()->add($this->property);
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
}
