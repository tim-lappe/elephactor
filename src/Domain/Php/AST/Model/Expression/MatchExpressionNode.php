<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class MatchExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<MatchArmNode> $arms
     */
    public function __construct(
        private readonly ExpressionNode $expression,
        private readonly array $arms
    ) {
        if ($arms === []) {
            throw new \InvalidArgumentException('Match expression requires arms');
        }

        parent::__construct(NodeKind::MATCH_EXPRESSION);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    /**
     * @return list<MatchArmNode>
     */
    public function arms(): array
    {
        return $this->arms;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->expression,
            ...$this->arms,
        ];
    }
}
