<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Name;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

final class FullyQualifiedNameNode extends QualifiedNameNode
{
    public function __construct(
        FullyQualifiedName $qualifiedName,
        Node $owner,
    ) {
        parent::__construct($qualifiedName, $owner, NodeKind::FULLY_QUALIFIED_NAME);
    }

    public function qualifiedName(): FullyQualifiedName
    {
        /** @var FullyQualifiedName $qualifiedName */
        $qualifiedName = parent::qualifiedName();

        return $qualifiedName;
    }

    public function fullyQualifiedName(): FullyQualifiedName
    {
        return $this->qualifiedName();
    }
}
