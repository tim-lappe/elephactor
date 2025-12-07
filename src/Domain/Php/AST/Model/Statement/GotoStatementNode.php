<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class GotoStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        Identifier $label
    ) {
        parent::__construct();

        $this->children()->add('label', new IdentifierNode($label));
    }

    public function label(): IdentifierNode
    {
        return $this->children()->getOne('label', IdentifierNode::class) ?? throw new \RuntimeException('Label not found');
    }
}
