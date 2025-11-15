<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\CastType;

final class CastExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly CastType $type,
        private readonly ExpressionNode $expression
    ) {
        parent::__construct(NodeKind::CAST_EXPRESSION);
    }

    public function type(): CastType
    {
        return $this->type;
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->expression];
    }
}
