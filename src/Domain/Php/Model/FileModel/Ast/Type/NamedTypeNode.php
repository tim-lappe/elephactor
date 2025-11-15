<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Type;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class NamedTypeNode extends AbstractNode implements TypeNode
{
    public function __construct(
        private readonly QualifiedName $name
    ) {
        parent::__construct(NodeKind::TYPE_REFERENCE);
    }

    public function name(): QualifiedName
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
