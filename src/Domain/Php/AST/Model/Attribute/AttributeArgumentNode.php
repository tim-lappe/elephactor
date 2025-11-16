<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class AttributeArgumentNode extends AbstractNode
{
    private readonly ?IdentifierNode $name;

    public function __construct(
        private readonly ExpressionNode $expression,
        ?Identifier $name = null
    ) {
        parent::__construct(NodeKind::ATTRIBUTE_ARGUMENT);

        $this->name = $name !== null ? new IdentifierNode($name, $this) : null;
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    public function name(): ?IdentifierNode
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->expression];

        if ($this->name !== null) {
            $children[] = $this->name;
        }

        return $children;
    }
}
