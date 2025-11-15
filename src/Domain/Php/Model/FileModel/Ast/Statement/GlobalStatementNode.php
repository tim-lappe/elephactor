<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class GlobalStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<ExpressionNode> $variables
     */
    public function __construct(
        private readonly array $variables
    ) {
        if ($variables === []) {
            throw new \InvalidArgumentException('Global statement requires at least one variable');
        }

        parent::__construct(NodeKind::GLOBAL_STATEMENT);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function variables(): array
    {
        return $this->variables;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->variables;
    }
}
