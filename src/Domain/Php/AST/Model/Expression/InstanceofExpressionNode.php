<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class InstanceofExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|TypeNode|ExpressionNode $classReference;

    public function __construct(
        private readonly ExpressionNode $expression,
        QualifiedName|TypeNode|ExpressionNode $classReference
    ) {
        parent::__construct(NodeKind::INSTANCEOF_EXPRESSION);

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference, $this)
            : $classReference;
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    public function classReference(): QualifiedNameNode|TypeNode|ExpressionNode
    {
        return $this->classReference;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->expression];

        $children[] = $this->classReference;

        return $children;
    }
}
