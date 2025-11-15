<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class EvalExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $code
    ) {
        parent::__construct(NodeKind::EVAL_EXPRESSION);
    }

    public function code(): ExpressionNode
    {
        return $this->code;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->code];
    }
}
