<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\EncapsedStringKind;

final class EncapsedStringExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<EncapsedStringPartNode> $parts
     */
    public function __construct(
        private readonly EncapsedStringKind $stringKind,
        private readonly array $parts
    ) {
        parent::__construct(NodeKind::ENCAPSED_STRING_EXPRESSION);
    }

    public function stringKind(): EncapsedStringKind
    {
        return $this->stringKind;
    }

    /**
     * @return list<EncapsedStringPartNode>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->parts;
    }
}
