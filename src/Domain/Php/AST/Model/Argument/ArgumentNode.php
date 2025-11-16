<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Argument;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class ArgumentNode extends AbstractNode
{
    private readonly ?IdentifierNode $name;

    public function __construct(
        private readonly ExpressionNode $expression,
        ?Identifier $name = null,
        private readonly bool $unpacked = false
    ) {
        parent::__construct(NodeKind::ARGUMENT);

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

    public function isUnpacked(): bool
    {
        return $this->unpacked;
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
