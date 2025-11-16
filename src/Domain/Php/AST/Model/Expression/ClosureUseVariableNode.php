<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class ClosureUseVariableNode extends AbstractNode
{
    private IdentifierNode $name;

    public function __construct(
        Identifier $name,
        private readonly bool $byReference = false
    ) {
        parent::__construct(NodeKind::CLOSURE_USE_VARIABLE);

        $this->name = new IdentifierNode($name, $this);
    }

    public function name(): IdentifierNode
    {
        return $this->name;
    }

    public function byReference(): bool
    {
        return $this->byReference;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->name];
    }
}
