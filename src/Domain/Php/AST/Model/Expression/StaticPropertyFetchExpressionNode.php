<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class StaticPropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier|ExpressionNode $property
    ) {
        parent::__construct();

        $classReferenceNode = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;
        $propertyNode = $property instanceof Identifier
            ? new IdentifierNode($property)
            : $property;

        $this->children()->add('classReference', $classReferenceNode);
        $this->children()->add('property', $propertyNode);
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->children()->getOne('classReference', QualifiedNameNode::class)
            ?? $this->children()->getOne('classReference', ExpressionNode::class)
            ?? throw new \RuntimeException('Class reference not found');
    }

    public function property(): IdentifierNode|ExpressionNode
    {
        return $this->children()->getOne('property', IdentifierNode::class)
            ?? $this->children()->getOne('property', ExpressionNode::class)
            ?? throw new \RuntimeException('Property not found');
    }
}
