<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class YieldExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly ?ExpressionNode $value,
        private readonly ?ExpressionNode $key = null
    ) {
        parent::__construct(NodeKind::YIELD_EXPRESSION);
    }

    public function value(): ?ExpressionNode
    {
        return $this->value;
    }

    public function key(): ?ExpressionNode
    {
        return $this->key;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [];

        if ($this->key !== null) {
            $children[] = $this->key;
        }

        if ($this->value !== null) {
            $children[] = $this->value;
        }

        return $children;
    }
}
