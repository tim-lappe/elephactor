<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class InstanceofExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|TypeNode|ExpressionNode $classReference;

    public function __construct(
        private readonly ExpressionNode $expression,
        QualifiedName|TypeNode|ExpressionNode $classReference
    ) {
        parent::__construct();

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;

        $this->children()->add($this->expression);
        $this->children()->add($this->classReference);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    public function classReference(): QualifiedNameNode|TypeNode|ExpressionNode
    {
        return $this->classReference;
    }
}
