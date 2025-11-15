<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Trivia;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class WhitespaceNode extends AbstractNode implements StatementNode, MemberNode
{
    public function __construct(
        private readonly int $lineBreaks
    ) {
        parent::__construct(NodeKind::WHITESPACE);

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
