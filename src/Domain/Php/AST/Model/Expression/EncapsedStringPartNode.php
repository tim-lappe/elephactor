<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final readonly class EncapsedStringPartNode extends AbstractNode
{
    public function __construct(
        private readonly string|ExpressionNode $part
    ) {
        parent::__construct();
    }

    public function part(): string|ExpressionNode
    {
        return $this->part;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->part instanceof ExpressionNode ? [$this->part] : [];
    }
}
