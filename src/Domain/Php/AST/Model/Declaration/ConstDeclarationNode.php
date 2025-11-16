<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\DeclarationNode;

final class ConstDeclarationNode extends AbstractNode implements DeclarationNode
{
    /**
     * @param list<ConstElementNode> $elements
     */
    public function __construct(
        private readonly array $elements
    ) {
        if ($elements === []) {
            throw new \InvalidArgumentException('Const declaration requires at least one element');
        }

        parent::__construct(NodeKind::CONST_DECLARATION);
    }

    /**
     * @return list<ConstElementNode>
     */
    public function elements(): array
    {
        return $this->elements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->elements;
    }
}
