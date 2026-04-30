<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Traversal;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;

interface NodeVisitor
{
    public function enter(Node $node, VisitorContext $context): void;

    public function leave(Node $node, VisitorContext $context): void;
}
