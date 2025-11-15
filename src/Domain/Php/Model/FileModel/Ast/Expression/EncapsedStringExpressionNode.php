<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\EncapsedStringKind;

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
