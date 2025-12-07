<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;

final class SwitchStatementNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<SwitchCaseNode> $cases
     */
    public function __construct(
        ExpressionNode $expression,
        array $cases
    ) {
        parent::__construct();

        $this->children()->add('expression', $expression);

        foreach ($cases as $case) {
            $this->children()->add('case', $case);
        }
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->getOne('expression', ExpressionNode::class) ?? throw new \RuntimeException('Switch expression missing');
    }

    /**
     * @return list<SwitchCaseNode>
     */
    public function cases(): array
    {
        return $this->children()->getAllOf('case', SwitchCaseNode::class);
    }
}
