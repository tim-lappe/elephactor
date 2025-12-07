<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class StaticStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StaticVariableNode> $variables
     */
    public function __construct(
        array $variables
    ) {
        if ($variables === []) {
            throw new \InvalidArgumentException('Static statement requires at least one variable');
        }

        parent::__construct();

        foreach ($variables as $variable) {
            $this->children()->add('variable', $variable);
        }
    }

    /**
     * @return list<StaticVariableNode>
     */
    public function variables(): array
    {
        return $this->children()->getAllOf('variable', StaticVariableNode::class);
    }
}
