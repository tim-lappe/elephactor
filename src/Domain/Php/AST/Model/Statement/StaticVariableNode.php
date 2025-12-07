<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class StaticVariableNode extends AbstractNode
{
    public function __construct(
        Identifier $name,
        private readonly ?ExpressionNode $defaultValue = null
    ) {
        parent::__construct();

        $this->children()->add('name', new IdentifierNode($name));

        if ($this->defaultValue !== null) {
            $this->children()->add('defaultValue', $this->defaultValue);
        }
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne('name', IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->children()->getOne('defaultValue', ExpressionNode::class);
    }
}
