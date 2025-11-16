<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class StaticPropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|ExpressionNode $classReference;
    private IdentifierNode|ExpressionNode $property;

    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier|ExpressionNode $property
    ) {
        parent::__construct(NodeKind::STATIC_PROPERTY_FETCH_EXPRESSION);

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference, $this)
            : $classReference;
        $this->property = $property instanceof Identifier
            ? new IdentifierNode($property, $this)
            : $property;
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->classReference;
    }

    public function property(): IdentifierNode|ExpressionNode
    {
        return $this->property;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->classReference,
            $this->property,
        ];
    }
}
