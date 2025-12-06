<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Visitor;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;

interface NodeVisitor
{
    public function enter(Node $node): void;

    public function leave(Node $node): void;
}