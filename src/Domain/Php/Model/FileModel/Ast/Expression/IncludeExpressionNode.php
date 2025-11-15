<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\IncludeKind;

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
