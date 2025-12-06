<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

interface ClassLikeNode extends DeclarationNode
{
    public function name(): IdentifierNode;
}
