<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\IncludeKind;

final class IncludeExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly IncludeKind $includeKind,
        ExpressionNode $path
    ) {
        parent::__construct();

        $this->children()->add('path', $path);
    }

    public function includeKind(): IncludeKind
    {
        return $this->includeKind;
    }

    public function path(): ExpressionNode
    {
        return $this->children()->getOne('path', ExpressionNode::class) ?? throw new \RuntimeException('Include path not found');
    }

}
