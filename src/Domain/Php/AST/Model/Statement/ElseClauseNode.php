<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ElseClauseNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        array $statements
    ) {
        parent::__construct();

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->children()->getAllOf('statement', StatementNode::class);
    }
}
