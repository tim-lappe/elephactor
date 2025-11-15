<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class AssignmentExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $target,
        private readonly ExpressionNode $value
    ) {
        parent::__construct(NodeKind::ASSIGNMENT_EXPRESSION);
    }

    public function target(): ExpressionNode
    {
        return $this->target;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->target,
            $this->value,
        ];
    }
}
