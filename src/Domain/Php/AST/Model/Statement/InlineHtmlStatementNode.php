<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class InlineHtmlStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly string $content
    ) {
        parent::__construct(NodeKind::INLINE_HTML_STATEMENT);
    }

    public function content(): string
    {
        return $this->content;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
