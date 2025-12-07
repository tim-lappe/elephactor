<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class GlobalStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<ExpressionNode> $variables
     */
    public function __construct(
        array $variables
    ) {
        if ($variables === []) {
            throw new \InvalidArgumentException('Global statement requires at least one variable');
        }

        parent::__construct();

        foreach ($variables as $variable) {
            $this->children()->add('variable', $variable);
        }
    }

    /**
     * @return list<ExpressionNode>
     */
    public function variables(): array
    {
        return $this->children()->getAllOf('variable', ExpressionNode::class);
    }
}
