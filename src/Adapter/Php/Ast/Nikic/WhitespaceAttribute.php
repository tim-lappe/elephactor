<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic;

use PhpParser\Node;

final class WhitespaceAttribute
{
    public const ATTRIBUTE = 'elephactor_blank_lines';

    public static function set(Node $node, int $lineBreaks): void
    {
        $node->setAttribute(self::ATTRIBUTE, $lineBreaks);
    }

    public static function get(Node $node): ?int
    {
        $value = $node->getAttribute(self::ATTRIBUTE);

        return is_int($value) ? $value : null;
    }
}
