<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class ClosureUseVariableNode extends AbstractNode
{
    public function __construct(
        Identifier $name,
        private readonly bool $byReference = false
    ) {
        parent::__construct();

        $this->children()->add('name', new IdentifierNode($name));
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne('name', IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
    }

    public function byReference(): bool
    {
        return $this->byReference;
    }

}
