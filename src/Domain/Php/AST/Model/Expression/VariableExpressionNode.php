<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class VariableExpressionNode extends AbstractNode implements ExpressionNode
{
    private IdentifierNode|ExpressionNode $name;

    public function __construct(
        Identifier|ExpressionNode $name
    ) {
        parent::__construct();

        $this->name = $name instanceof Identifier ? new IdentifierNode($name) : $name;
        $this->children()->add($this->name);
    }

    public function name(): IdentifierNode|ExpressionNode
    {
        return $this->name;
    }
}
