<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class ClassConstantFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|ExpressionNode $classReference;
    private IdentifierNode $constant;

    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier $constant
    ) {
        parent::__construct();

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;
        $this->constant = new IdentifierNode($constant);

        $this->children()->add($this->constant);
        $this->children()->add($this->classReference);
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->classReference;
    }

    public function constant(): IdentifierNode
    {
        return $this->constant;
    }
}
