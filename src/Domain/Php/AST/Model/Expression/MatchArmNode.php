<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class MatchArmNode extends AbstractNode
{
    /**
     * @param list<ExpressionNode> $conditions
     */
    public function __construct(
        array $conditions,
        ExpressionNode $body
    ) {
        parent::__construct();

        foreach ($conditions as $condition) {
            $this->children()->add('condition', $condition);
        }

        $this->children()->add('body', $body);
    }

    /**
     * @return list<ExpressionNode>
     */
    public function conditions(): array
    {
        return $this->children()->getAllOf('condition', ExpressionNode::class);
    }

    public function body(): ExpressionNode
    {
        return $this->children()->getOne('body', ExpressionNode::class) ?? throw new \RuntimeException('Match arm body not found');
    }

    public function isDefault(): bool
    {
        return $this->children()->getAllOf('condition', ExpressionNode::class) === [];
    }

}
