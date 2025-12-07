<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class TernaryExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        ExpressionNode $condition,
        ?ExpressionNode $ifTrue,
        ExpressionNode $ifFalse
    ) {
        parent::__construct();

        $this->children()->add('condition', $condition);

        if ($ifTrue !== null) {
            $this->children()->add('ifTrue', $ifTrue);
        }

        $this->children()->add('ifFalse', $ifFalse);
    }

    public function condition(): ExpressionNode
    {
        return $this->children()->getOne('condition', ExpressionNode::class) ?? throw new \RuntimeException('Condition not found');
    }

    public function ifTrue(): ?ExpressionNode
    {
        return $this->children()->getOne('ifTrue', ExpressionNode::class);
    }

    public function ifFalse(): ExpressionNode
    {
        return $this->children()->getOne('ifFalse', ExpressionNode::class) ?? throw new \RuntimeException('False branch not found');
    }

}
