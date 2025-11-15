<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\WhitespaceAttribute;

final class WhitespaceAwarePrettyPrinter extends Standard
{
    protected function pStmts(array $nodes, bool $indent = true): string
    {
        if ($indent) {
            $this->indent();
        }

        $result = '';
        foreach ($nodes as $node) {
            if ($this->isWhitespaceNode($node)) {
                $result .= $this->formatBlankLines($node);
                continue;
            }

            $comments = $node->getComments();
            if ($comments !== []) {
                $result .= $this->nl . $this->pComments($comments);
                if ($node instanceof Stmt\Nop) {
                    continue;
                }
            }

            $result .= $this->nl . $this->p($node);
        }

        if ($indent) {
            $this->outdent();
        }

        return $result;
    }

    private function isWhitespaceNode(Node $node): bool
    {
        if (!$node instanceof Stmt\Nop) {
            return false;
        }

        return WhitespaceAttribute::get($node) !== null;
    }

    private function formatBlankLines(Node $node): string
    {
        $lineBreaks = WhitespaceAttribute::get($node) ?? 0;

        if ($lineBreaks < 1) {
            return '';
        }

        return str_repeat($this->newline, $lineBreaks);
    }
}
