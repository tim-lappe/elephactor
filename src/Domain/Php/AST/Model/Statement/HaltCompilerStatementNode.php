<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class HaltCompilerStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly string $remainingContent
    ) {
        parent::__construct();
    }

    public function remainingContent(): string
    {
        return $this->remainingContent;
    }
}
