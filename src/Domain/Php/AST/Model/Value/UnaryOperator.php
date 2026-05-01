<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

enum UnaryOperator: string
{
    case PLUS = '+';
    case MINUS = '-';
    case BITWISE_NOT = '~';
    case LOGICAL_NOT = '!';
    case PRE_INCREMENT = '++pre';
    case PRE_DECREMENT = '--pre';
    case POST_INCREMENT = '++post';
    case POST_DECREMENT = '--post';
}
