<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum IncludeKind: string
{
    case INCLUDE = 'include';
    case INCLUDE_ONCE = 'include_once';
    case REQUIRE = 'require';
    case REQUIRE_ONCE = 'require_once';
}
