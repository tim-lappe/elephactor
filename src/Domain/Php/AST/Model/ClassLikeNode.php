<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;

interface ClassLikeNode extends DeclarationNode
{
    public function name(): IdentifierNode;
}
