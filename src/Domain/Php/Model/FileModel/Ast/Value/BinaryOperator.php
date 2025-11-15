<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum BinaryOperator: string
{
    case PLUS = '+';
    case MINUS = '-';
    case MULTIPLY = '*';
    case DIVIDE = '/';
    case MODULO = '%';
    case POWER = '**';
    case CONCAT = '.';
    case BITWISE_AND = '&';
    case BITWISE_OR = '|';
    case BITWISE_XOR = '^';
    case SHIFT_LEFT = '<<';
    case SHIFT_RIGHT = '>>';
    case LOGICAL_AND = '&&';
    case LOGICAL_OR = '||';
    case LOGICAL_XOR = 'xor';
    case EQUAL = '==';
    case IDENTICAL = '===';
    case NOT_EQUAL = '!=';
    case NOT_IDENTICAL = '!==';
    case GREATER = '>';
    case GREATER_EQUAL = '>=';
    case SMALLER = '<';
    case SMALLER_EQUAL = '<=';
    case SPACESHIP = '<=>';
    case COALESCE = '??';
    case AND = 'and';
    case OR = 'or';
}
