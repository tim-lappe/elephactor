<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

enum EncapsedStringKind: string
{
    case DOUBLE_QUOTED = 'double_quoted';
    case HEREDOC = 'heredoc';
    case NOWDOC = 'nowdoc';
}
