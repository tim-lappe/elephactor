<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\DeclarationNode;

final class ConstDeclarationNode extends AbstractNode implements DeclarationNode
{
    /**
     * @param list<ConstElementNode> $elements
     */
    public function __construct(
        array $elements
    ) {
        if ($elements === []) {
            throw new \InvalidArgumentException('Const declaration requires at least one element');
        }

        parent::__construct();

        foreach ($elements as $element) {
            $this->children()->add("element", $element);
        }
    }

    /**
     * @return list<ConstElementNode>
     */
    public function elements(): array
    {
        return $this->children()->getAllOf("element", ConstElementNode::class);
    }
}
