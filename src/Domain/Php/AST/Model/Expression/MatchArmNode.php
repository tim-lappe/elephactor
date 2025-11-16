<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class MatchArmNode extends AbstractNode
{
    /**
     * @param list<ExpressionNode> $conditions
     */
    public function __construct(
        private readonly array $conditions,
        private readonly ExpressionNode $body
    ) {
        parent::__construct(NodeKind::MATCH_ARM);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    public function body(): ExpressionNode
    {
        return $this->body;
    }

    public function isDefault(): bool
    {
        return $this->conditions === [];
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            ...$this->conditions,
            $this->body,
        ];
    }
}
