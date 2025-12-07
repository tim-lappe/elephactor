<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ListItemNode extends AbstractNode
{
    public function __construct(
        private readonly ?ExpressionNode $key,
        private readonly ExpressionNode $value
    ) {
        parent::__construct();

        if ($this->key !== null) {
            $this->children()->add('key', $this->key);
        }

        $this->children()->add('value', $this->value);
    }

    public function key(): ?ExpressionNode
    {
        return $this->key;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

}
