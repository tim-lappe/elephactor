<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\EncapsedStringKind;

final class EncapsedStringExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<EncapsedStringPartNode> $parts
     */
    public function __construct(
        private readonly EncapsedStringKind $stringKind,
        array $parts
    ) {
        parent::__construct();

        foreach ($parts as $part) {
            $this->children()->add('part', $part);
        }
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
        return $this->children()->getAllOf('part', EncapsedStringPartNode::class);
    }

}
