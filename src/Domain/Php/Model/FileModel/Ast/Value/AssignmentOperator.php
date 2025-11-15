<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

enum AssignmentOperator: string
{
    case ASSIGN = '=';
    case PLUS = '+=';
    case MINUS = '-=';
    case MULTIPLY = '*=';
    case DIVIDE = '/=';
    case MODULO = '%=';
    case POWER = '**=';
    case CONCAT = '.=';
    case BITWISE_AND = '&=';
    case BITWISE_OR = '|=';
    case BITWISE_XOR = '^=';
    case SHIFT_LEFT = '<<=';
    case SHIFT_RIGHT = '>>=';
    case COALESCE = '??=';
}
