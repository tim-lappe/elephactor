<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

enum UseKind: string
{
    case CLASS_IMPORT = 'class';
    case FUNCTION_IMPORT = 'function';
    case CONSTANT_IMPORT = 'const';
}
