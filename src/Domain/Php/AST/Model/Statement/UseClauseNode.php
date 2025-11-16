<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class UseClauseNode extends AbstractNode
{
    private QualifiedNameNode $name;
    private readonly ?IdentifierNode $alias;

    public function __construct(
        QualifiedName $name,
        ?Identifier $alias = null
    ) {
        parent::__construct(NodeKind::USE_CLAUSE);

        $this->name = new QualifiedNameNode($name, $this);
        $this->alias = $alias !== null ? new IdentifierNode($alias, $this) : null;
    }

    public function name(): QualifiedNameNode
    {
        return $this->name;
    }

    public function alias(): ?IdentifierNode
    {
        return $this->alias;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [$this->name];

        if ($this->alias !== null) {
            $children[] = $this->alias;
        }

        return $children;
    }
}
