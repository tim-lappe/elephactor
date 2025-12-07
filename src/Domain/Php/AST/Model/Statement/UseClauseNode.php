<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class UseClauseNode extends AbstractNode
{
    public function __construct(
        QualifiedName $name,
        ?Identifier $alias = null
    ) {
        parent::__construct();

        $this->children()->add('name', new QualifiedNameNode($name));
        if ($alias !== null) {
            $this->children()->add('alias', new IdentifierNode($alias));
        }
    }

    public function name(): QualifiedNameNode
    {
        return $this->children()->getOne('name', QualifiedNameNode::class) ?? throw new \RuntimeException('Use clause name not found');
    }

    public function alias(): ?IdentifierNode
    {
        return $this->children()->getOne('alias', IdentifierNode::class);
    }
}
