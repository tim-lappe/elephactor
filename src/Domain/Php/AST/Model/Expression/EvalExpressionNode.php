<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class EvalExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ExpressionNode $code
    ) {
        parent::__construct();

        $this->children()->add('code', $code);
    }

    public function code(): ExpressionNode
    {
        return $this->children()->getOne('code', ExpressionNode::class) ?? throw new \RuntimeException('Code expression not found');
    }

}
