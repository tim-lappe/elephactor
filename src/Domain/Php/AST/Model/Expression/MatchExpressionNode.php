<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class MatchExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<MatchArmNode> $arms
     */
    public function __construct(
        ExpressionNode $expression,
        array $arms
    ) {
        if ($arms === []) {
            throw new \InvalidArgumentException('Match expression requires arms');
        }

        parent::__construct();

        $this->children()->add('expression', $expression);

        foreach ($arms as $arm) {
            $this->children()->add('arm', $arm);
        }
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->getOne('expression', ExpressionNode::class) ?? throw new \RuntimeException('Match subject not found');
    }

    /**
     * @return list<MatchArmNode>
     */
    public function arms(): array
    {
        return $this->children()->getAllOf('arm', MatchArmNode::class);
    }

}
