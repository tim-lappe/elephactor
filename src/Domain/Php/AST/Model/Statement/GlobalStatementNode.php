<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class GlobalStatementNode extends AbstractNode implements StatementNode
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

        parent::__construct();
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
