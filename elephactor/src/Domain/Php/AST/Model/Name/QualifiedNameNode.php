<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Name;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

class QualifiedNameNode extends AbstractNode
{
    private QualifiedName $qualifiedName;

    public function __construct(
        QualifiedName $qualifiedName,
    ) {
        parent::__construct();
        $this->qualifiedName = $qualifiedName;
    }

    public function qualifiedName(): QualifiedName
    {
        return $this->qualifiedName;
    }

    public function replaceQualifiedName(QualifiedName $qualifiedName): void
    {
        $this->qualifiedName = $qualifiedName;
    }

    public function __toString(): string
    {
        return $this->qualifiedName->__toString();
    }
}
