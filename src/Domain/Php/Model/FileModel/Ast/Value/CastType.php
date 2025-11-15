<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum CastType: string
{
    case ARRAY = 'array';
    case BOOL = 'bool';
    case INT = 'int';
    case FLOAT = 'float';
    case STRING = 'string';
    case OBJECT = 'object';
    case UNSET = 'unset';
}
