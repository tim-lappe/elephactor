<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class AttributeArgumentNode extends AbstractNode
{
    public function __construct(
        private readonly ExpressionNode $expression,
        private readonly ?Identifier $name = null
    ) {
        parent::__construct(NodeKind::ATTRIBUTE_ARGUMENT);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    public function name(): ?Identifier
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->expression];
    }
}
