<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class VariableExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        Identifier|ExpressionNode $name
    ) {
        parent::__construct();

        $nameNode = $name instanceof Identifier ? new IdentifierNode($name) : $name;
        $this->children()->add('name', $nameNode);
    }

    public function name(): IdentifierNode|ExpressionNode
    {
        return $this->children()->getOne('name', IdentifierNode::class)
            ?? $this->children()->getOne('name', ExpressionNode::class)
            ?? throw new \RuntimeException('Variable name not found');
    }
}
