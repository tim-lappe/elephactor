<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

interface ClassLikeNode extends DeclarationNode
{
    public function name(): Identifier;

    public function changeName(Identifier $name): void;
}
