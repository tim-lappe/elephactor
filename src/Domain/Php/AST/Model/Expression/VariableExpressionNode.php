<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class VariableExpressionNode extends AbstractNode implements ExpressionNode
{
    private IdentifierNode|ExpressionNode $name;

    public function __construct(
        Identifier|ExpressionNode $name
    ) {
        parent::__construct(NodeKind::VARIABLE_EXPRESSION);

        $this->name = $name instanceof Identifier ? new IdentifierNode($name, $this) : $name;
    }

    public function name(): IdentifierNode|ExpressionNode
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->name];
    }
}
