<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Name;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

class QualifiedNameNode extends AbstractNode
{
    protected QualifiedName $qualifiedName;

    public function __construct(
        QualifiedName $qualifiedName,
        private readonly Node $owner,
        NodeKind $kind = NodeKind::QUALIFIED_NAME,
    ) {
        parent::__construct($kind);
        $this->qualifiedName = $qualifiedName;
    }

    public function owner(): Node
    {
        return $this->owner;
    }

    public function qualifiedName(): QualifiedName
    {
        return $this->qualifiedName;
    }

    public function changeQualifiedName(QualifiedName $qualifiedName): void
    {
        $this->qualifiedName = $qualifiedName;
    }

    public function __toString(): string
    {
        return $this->qualifiedName->__toString();
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
