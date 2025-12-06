<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final readonly class StaticStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StaticVariableNode> $variables
     */
    public function __construct(
        private readonly array $variables
    ) {
        if ($variables === []) {
            throw new \InvalidArgumentException('Static statement requires at least one variable');
        }

        parent::__construct();
    }

    /**
     * @return list<StaticVariableNode>
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
