<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Type;

enum SpecialType: string
{
    case ARRAY = 'array';
    case CALLABLE = 'callable';
    case ITERABLE = 'iterable';
    case VOID = 'void';
    case NEVER = 'never';
    case MIXED = 'mixed';
    case NULL = 'null';
    case FALSE = 'false';
    case TRUE = 'true';
    case OBJECT = 'object';
    case STATIC = 'static';
    case SELF = 'self';
    case PARENT = 'parent';
}
