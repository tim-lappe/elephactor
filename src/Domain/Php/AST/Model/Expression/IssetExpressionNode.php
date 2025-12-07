<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class IssetExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ExpressionNode> $expressions
     */
    public function __construct(
        private readonly array $expressions
    ) {
        if ($expressions === []) {
            throw new \InvalidArgumentException('Isset expression requires at least one operand');
        }

        parent::__construct();

        foreach ($expressions as $expression) {
            $this->children()->add('expression', $expression);
        }
    }

    /**
     * @return list<ExpressionNode>
     */
    public function expressions(): array
    {
        return $this->expressions;
    }

}
