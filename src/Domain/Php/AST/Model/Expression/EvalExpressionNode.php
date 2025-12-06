<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final readonly class EvalExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ExpressionNode $code
    ) {
        parent::__construct();
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
