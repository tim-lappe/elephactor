<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\DeclarationNode;

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
