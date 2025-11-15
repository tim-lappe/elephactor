<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class StaticPropertyFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly QualifiedName|ExpressionNode $classReference,
        private readonly Identifier|ExpressionNode $property
    ) {
        parent::__construct(NodeKind::STATIC_PROPERTY_FETCH_EXPRESSION);
    }

    public function classReference(): QualifiedName|ExpressionNode
    {
        return $this->classReference;
    }

    public function property(): Identifier|ExpressionNode
    {
        return $this->property;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [];

        if ($this->classReference instanceof ExpressionNode) {
            $children[] = $this->classReference;
        }

        if ($this->property instanceof ExpressionNode) {
            $children[] = $this->property;
        }

        return $children;
    }
}
