<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class GotoStatementNode extends AbstractNode implements StatementNode
{
    private IdentifierNode $label;

    public function __construct(
        Identifier $label
    ) {
        parent::__construct(NodeKind::GOTO_STATEMENT);

        $this->label = new IdentifierNode($label, $this);
    }

    public function label(): IdentifierNode
    {
        return $this->label;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->label];
    }
}
