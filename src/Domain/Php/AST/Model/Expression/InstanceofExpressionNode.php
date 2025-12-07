<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class InstanceofExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ExpressionNode $expression,
        QualifiedName|TypeNode|ExpressionNode $classReference
    ) {
        parent::__construct();

        $classReferenceNode = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;

        $this->children()->add('expression', $expression);
        $this->children()->add('classReference', $classReferenceNode);
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->getOne('expression', ExpressionNode::class) ?? throw new \RuntimeException('Expression not found');
    }

    public function classReference(): QualifiedNameNode|TypeNode|ExpressionNode
    {
        return $this->children()->getOne('classReference', QualifiedNameNode::class)
            ?? $this->children()->getOne('classReference', TypeNode::class)
            ?? $this->children()->getOne('classReference', ExpressionNode::class)
            ?? throw new \RuntimeException('Class reference not found');
    }
}
