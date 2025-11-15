<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class ConstantFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly QualifiedName $name
    ) {
        parent::__construct(NodeKind::CONSTANT_FETCH_EXPRESSION);
    }

    public function name(): QualifiedName
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
