<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum Visibility: string
{
    case PUBLIC = 'public';
    case PROTECTED = 'protected';
    case PRIVATE = 'private';
}
