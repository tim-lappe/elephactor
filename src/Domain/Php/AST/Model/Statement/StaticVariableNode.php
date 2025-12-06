<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final readonly class StaticVariableNode extends AbstractNode
{
    private IdentifierNode $name;

    public function __construct(
        Identifier $name,
        private readonly ?ExpressionNode $defaultValue = null
    ) {
        parent::__construct();

        $this->name = new IdentifierNode($name, $this);
    }

    public function name(): IdentifierNode
    {
        return $this->name;
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->defaultValue;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->name];

        if ($this->defaultValue !== null) {
            $children[] = $this->defaultValue;
        }

        return $children;
    }
}
