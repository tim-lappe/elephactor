<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

final class EncapsedStringPartNode extends AbstractNode
{
    public function __construct(
        private readonly string|ExpressionNode $part
    ) {
        parent::__construct(NodeKind::ENCAPSED_STRING_PART);
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
