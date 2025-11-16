<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\IncludeKind;

final class IncludeExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly IncludeKind $includeKind,
        private readonly ExpressionNode $path
    ) {
        parent::__construct(NodeKind::INCLUDE_EXPRESSION);
    }

    public function includeKind(): IncludeKind
    {
        return $this->includeKind;
    }

    public function path(): ExpressionNode
    {
        return $this->path;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->path];
    }
}
