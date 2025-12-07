<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class ClassConstantFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier $constant
    ) {
        parent::__construct();

        $classReferenceNode = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;
        $constantNode = new IdentifierNode($constant);

        $this->children()->add('constant', $constantNode);
        $this->children()->add('classReference', $classReferenceNode);
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->children()->getOne('classReference', QualifiedNameNode::class)
            ?? $this->children()->getOne('classReference', ExpressionNode::class)
            ?? throw new \RuntimeException('Class reference not found');
    }

    public function constant(): IdentifierNode
    {
        return $this->children()->getOne('constant', IdentifierNode::class) ?? throw new \RuntimeException('Constant not found');
    }
}
