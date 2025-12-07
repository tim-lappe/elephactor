<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Attribute;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class AttributeArgumentNode extends AbstractNode
{
    public function __construct(
        ExpressionNode $expression,
        ?Identifier $name = null
    ) {
        parent::__construct();

        $this->children()->add("expression", $expression);

        if ($name !== null) {
            $this->children()->add("name", new IdentifierNode($name));
        }
    }

    public function expression(): ExpressionNode
    {
        return $this->children()->getOne("expression", ExpressionNode::class) ?? throw new \RuntimeException('Expression not found');
    }

    public function name(): ?IdentifierNode
    {
        return $this->children()->getOne("name", IdentifierNode::class);
    }
}
