<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Trivia;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class WhitespaceNode extends AbstractNode implements StatementNode, MemberNode
{
    public function __construct(
        private readonly int $lineBreaks
    ) {
        parent::__construct();

        if ($lineBreaks < 1) {
            throw new \InvalidArgumentException('Whitespace nodes require at least one line break');
        }
    }

    public function lineBreaks(): int
    {
        return $this->lineBreaks;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
