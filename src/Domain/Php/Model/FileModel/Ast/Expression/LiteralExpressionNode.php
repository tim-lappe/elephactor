<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\LiteralValue;

final class LiteralExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly LiteralValue $value
    ) {
        parent::__construct(NodeKind::LITERAL_EXPRESSION);
    }

    public function value(): LiteralValue
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
