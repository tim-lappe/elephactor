<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class DeclareDirectiveNode extends AbstractNode
{
    public function __construct(
        Identifier $name,
        ExpressionNode $value
    ) {
        parent::__construct();

        $this->children()->add('name', new IdentifierNode($name));
        $this->children()->add('value', $value);
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne('name', IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
    }

    public function value(): ExpressionNode
    {
        return $this->children()->getOne('value', ExpressionNode::class) ?? throw new \RuntimeException('Value not found');
    }
}
