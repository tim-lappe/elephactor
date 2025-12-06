<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class NamedTypeNode extends AbstractNode implements TypeNode
{
    private QualifiedNameNode $nameNode;

    public function __construct(
        QualifiedName $name
    ) {
        parent::__construct();
        $this->nameNode = new QualifiedNameNode($name);
        $this->children()->add($this->nameNode);
    }

    public function name(): QualifiedNameNode
    {
        return $this->nameNode;
    }
}
