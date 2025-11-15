<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class InstanceofExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $expression,
        private readonly QualifiedName|TypeNode|ExpressionNode $classReference
    ) {
        parent::__construct(NodeKind::INSTANCEOF_EXPRESSION);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    public function classReference(): QualifiedName|TypeNode|ExpressionNode
    {
        return $this->classReference;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->expression];

        if ($this->classReference instanceof ExpressionNode || $this->classReference instanceof TypeNode) {
            $children[] = $this->classReference;
        }

        return $children;
    }
}
