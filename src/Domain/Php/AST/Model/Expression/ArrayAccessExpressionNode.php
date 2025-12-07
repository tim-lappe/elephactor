<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ArrayAccessExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ExpressionNode $array,
        ?ExpressionNode $offset = null
    ) {
        parent::__construct();

        $this->children()->add('array', $array);

        if ($offset !== null) {
            $this->children()->add('offset', $offset);
        }
    }

    public function array(): ExpressionNode
    {
        return $this->children()->getOne('array', ExpressionNode::class) ?? throw new \RuntimeException('Array expression not found');
    }

    public function offset(): ?ExpressionNode
    {
        return $this->children()->getOne('offset', ExpressionNode::class);
    }

}
