<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class UseClauseNode extends AbstractNode
{
    public function __construct(
        private readonly QualifiedName $name,
        private readonly ?Identifier $alias = null
    ) {
        parent::__construct(NodeKind::USE_CLAUSE);
    }

    public function name(): QualifiedName
    {
        return $this->name;
    }

    public function alias(): ?Identifier
    {
        return $this->alias;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
