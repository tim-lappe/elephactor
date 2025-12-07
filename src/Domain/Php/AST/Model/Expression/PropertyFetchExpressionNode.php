<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class PropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ExpressionNode $object,
        Identifier|ExpressionNode $property,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct();

        $propertyNode = $property instanceof Identifier ? new IdentifierNode($property) : $property;

        $this->children()->add('object', $object);
        $this->children()->add('property', $propertyNode);
    }

    public function object(): ExpressionNode
    {
        return $this->children()->getOne('object', ExpressionNode::class) ?? throw new \RuntimeException('Object expression not found');
    }

    public function property(): IdentifierNode|ExpressionNode
    {
        return $this->children()->getOne('property', IdentifierNode::class)
            ?? $this->children()->getOne('property', ExpressionNode::class)
            ?? throw new \RuntimeException('Property not found');
    }

    public function isNullsafe(): bool
    {
        return $this->nullsafe;
    }
}
