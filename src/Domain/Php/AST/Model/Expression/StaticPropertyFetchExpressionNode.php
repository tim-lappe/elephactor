<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class StaticPropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|ExpressionNode $classReference;
    private IdentifierNode|ExpressionNode $property;

    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier|ExpressionNode $property
    ) {
        parent::__construct();

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;
        $this->property = $property instanceof Identifier
            ? new IdentifierNode($property)
            : $property;

        $this->children()->add($this->classReference);
        $this->children()->add($this->property);
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->classReference;
    }

    public function property(): IdentifierNode|ExpressionNode
    {
        return $this->property;
    }
}
