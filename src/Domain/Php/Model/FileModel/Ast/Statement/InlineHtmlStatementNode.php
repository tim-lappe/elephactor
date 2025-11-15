<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

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
