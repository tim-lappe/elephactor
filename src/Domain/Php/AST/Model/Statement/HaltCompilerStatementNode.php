<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class HaltCompilerStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly string $remainingContent
    ) {
        parent::__construct(NodeKind::HALT_COMPILER_STATEMENT);
    }

    public function remainingContent(): string
    {
        return $this->remainingContent;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
