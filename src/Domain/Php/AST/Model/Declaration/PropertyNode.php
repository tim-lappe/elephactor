<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final readonly class PropertyNode extends AbstractNode
{
    public function __construct(
        Identifier $name,
        ?ExpressionNode $defaultValue = null
    ) {
        parent::__construct();

        $name = new IdentifierNode($name);
        $this->children()->add("name", $name);

        if ($defaultValue !== null) {
            $this->children()->add("defaultValue", $defaultValue);
        }
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne("name", IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->children()->getOne("defaultValue", ExpressionNode::class);
    }
}
