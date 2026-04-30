<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic;

use PhpParser\Node;

final class TokenSpanExtraBlankLines
{
    /**
     * @param list<\PhpParser\Token> $tokens
     */
    public static function betweenNodes(Node $previous, Node $next, array $tokens): int
    {
        $start = $previous->getEndTokenPos() + 1;
        $nextStart = $next->getStartTokenPos();
        $nextComments = $next->getComments();
        if ($nextComments !== []) {
            $nextStart = min($nextStart, $nextComments[0]->getStartTokenPos());
        }
        $end = $nextStart - 1;
        if ($start > $end || $start < 0) {
            return 0;
        }

        $buf = '';
        $count = count($tokens);
        for ($i = $start; $i <= $end && $i < $count; $i++) {
            $buf .= $tokens[$i]->text;
        }

        return max(0, substr_count($buf, "\n") - 1);
    }
}
