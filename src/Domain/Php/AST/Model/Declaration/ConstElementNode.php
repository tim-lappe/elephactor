<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class ConstElementNode extends AbstractNode
{
    private IdentifierNode $name;

    public function __construct(
        Identifier $name,
        private readonly ExpressionNode $value
    ) {
        parent::__construct(NodeKind::CONST_ELEMENT);

        $this->name = new IdentifierNode($name, $this);
    }

    public function name(): IdentifierNode
    {
        return $this->name;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->name,
            $this->value,
        ];
    }
}
