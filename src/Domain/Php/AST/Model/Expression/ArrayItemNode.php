<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ArrayItemNode extends AbstractNode
{
    public function __construct(
        ExpressionNode $value,
        ?ExpressionNode $key = null,
        private readonly bool $byReference = false,
        private readonly bool $unpack = false
    ) {
        parent::__construct();

        if ($key !== null) {
            $this->children()->add('key', $key);
        }

        $this->children()->add('value', $value);
    }

    public function value(): ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class) ?? throw new \RuntimeException('Value expression not found');
    }

    public function key(): ?ExpressionNode
    {
        return $this->children()->getOne('key', ExpressionNode::class);
    }

    public function byReference(): bool
    {
        return $this->byReference;
    }

    public function isUnpacked(): bool
    {
        return $this->unpack;
    }

}
