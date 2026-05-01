<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class DeclareStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<DeclareDirectiveNode> $directives
     * @param list<StatementNode>        $blockStatements
     */
    public function __construct(
        array $directives,
        array $blockStatements = [],
        ?StatementNode $singleStatement = null
    ) {
        if ($directives === []) {
            throw new \InvalidArgumentException('Declare statement requires directives');
        }

        if ($blockStatements !== [] && $singleStatement !== null) {
            throw new \InvalidArgumentException('Declare statement cannot have both block and single statement');
        }

        parent::__construct();

        foreach ($directives as $directive) {
            $this->children()->add('directive', $directive);
        }

        if ($singleStatement !== null) {
            $this->children()->add('singleStatement', $singleStatement);
        }

        foreach ($blockStatements as $statement) {
            $this->children()->add('blockStatement', $statement);
        }
    }

    /**
     * @return list<DeclareDirectiveNode>
     */
    public function directives(): array
    {
        return $this->children()->getAllOf('directive', DeclareDirectiveNode::class);
    }

    /**
     * @return list<StatementNode>
     */
    public function blockStatements(): array
    {
        return $this->children()->getAllOf('blockStatement', StatementNode::class);
    }

    public function singleStatement(): ?StatementNode
    {
        return $this->children()->getOne('singleStatement', StatementNode::class);
    }

    public function hasBlock(): bool
    {
        return $this->children()->getAllOf('blockStatement', StatementNode::class) !== [];
    }
}
