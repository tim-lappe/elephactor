<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class ClassConstantFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly QualifiedName|ExpressionNode $classReference,
        private readonly Identifier $constant
    ) {
        parent::__construct(NodeKind::CLASS_CONSTANT_FETCH_EXPRESSION);
    }

    public function classReference(): QualifiedName|ExpressionNode
    {
        return $this->classReference;
    }

    public function constant(): Identifier
    {
        return $this->constant;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->classReference instanceof ExpressionNode ? [$this->classReference] : [];
    }
}
