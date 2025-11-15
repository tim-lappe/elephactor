<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum EncapsedStringKind: string
{
    case DOUBLE_QUOTED = 'double_quoted';
    case HEREDOC = 'heredoc';
    case NOWDOC = 'nowdoc';
}
