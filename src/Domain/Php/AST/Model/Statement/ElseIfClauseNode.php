<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class ElseIfClauseNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        ExpressionNode $condition,
        array $statements
    ) {
        parent::__construct();

        $this->children()->add('condition', $condition);

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    public function condition(): ExpressionNode
    {
        return $this->children()->getOne('condition', ExpressionNode::class) ?? throw new \RuntimeException('Else-if condition missing');
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->children()->getAllOf('statement', StatementNode::class);
    }
}
