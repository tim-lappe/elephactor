<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class AssignmentExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ExpressionNode $target,
        ExpressionNode $value
    ) {
        parent::__construct();

        $this->children()->add('target', $target);
        $this->children()->add('value', $value);
    }

    public function target(): ExpressionNode
    {
        return $this->children()->getOne('target', ExpressionNode::class) ?? throw new \RuntimeException('Target expression not found');
    }

    public function value(): ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class) ?? throw new \RuntimeException('Value expression not found');
    }

}
