<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum ParameterPassingMode: string
{
    case BY_VALUE = 'by_value';
    case BY_REFERENCE = 'by_reference';
}
