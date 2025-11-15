<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class DeclareStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<DeclareDirectiveNode> $directives
     * @param list<StatementNode>        $blockStatements
     */
    public function __construct(
        private readonly array $directives,
        private readonly array $blockStatements = [],
        private readonly ?StatementNode $singleStatement = null
    ) {
        if ($directives === []) {
            throw new \InvalidArgumentException('Declare statement requires directives');
        }

        if ($blockStatements !== [] && $singleStatement !== null) {
            throw new \InvalidArgumentException('Declare statement cannot have both block and single statement');
        }

        parent::__construct(NodeKind::DECLARE_STATEMENT);
    }

    /**
     * @return list<DeclareDirectiveNode>
     */
    public function directives(): array
    {
        return $this->directives;
    }

    /**
     * @return list<StatementNode>
     */
    public function blockStatements(): array
    {
        return $this->blockStatements;
    }

    public function singleStatement(): ?StatementNode
    {
        return $this->singleStatement;
    }

    public function hasBlock(): bool
    {
        return $this->blockStatements !== [];
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = $this->directives;

        if ($this->singleStatement !== null) {
            $children[] = $this->singleStatement;
        }

        return array_merge($children, $this->blockStatements);
    }
}
