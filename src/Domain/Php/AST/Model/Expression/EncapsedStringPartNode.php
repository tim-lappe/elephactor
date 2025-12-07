<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class EncapsedStringPartNode extends AbstractNode
{
    public function __construct(
        private readonly string|ExpressionNode $part
    ) {
        parent::__construct();

        if ($this->part instanceof ExpressionNode) {
            $this->children()->add('part', $this->part);
        }
    }

    public function part(): string|ExpressionNode
    {
        $node = $this->children()->getOne('part', ExpressionNode::class);

        return $node ?? $this->part;
    }

}
