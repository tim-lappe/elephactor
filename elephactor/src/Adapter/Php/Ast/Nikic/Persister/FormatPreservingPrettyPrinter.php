<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister;

use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\WhitespaceAttribute;

final class FormatPreservingPrettyPrinter extends Standard
{
    protected function pStmts(array $nodes, bool $indent = true): string
    {
        if ($indent) {
            $this->indent();
        }

        $result = '';
        foreach ($nodes as $node) {
            $comments = $node->getComments();
            if ($comments !== []) {
                $result .= $this->nl . $this->pComments($comments);
                if ($node instanceof Stmt\Nop) {
                    continue;
                }
            }

            $extraLeading = WhitespaceAttribute::get($node) ?? 0;
            $result .= str_repeat($this->newline, $extraLeading) . $this->nl . $this->p($node);
        }

        if ($indent) {
            $this->outdent();
        }

        return $result;
    }
}
