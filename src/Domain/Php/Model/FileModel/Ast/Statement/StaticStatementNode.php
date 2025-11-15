<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

final class StaticStatementNode extends AbstractNode implements StatementNode
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

        parent::__construct(NodeKind::STATIC_STATEMENT);
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
