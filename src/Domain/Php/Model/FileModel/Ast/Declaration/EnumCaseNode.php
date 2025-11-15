<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class EnumCaseNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        private readonly Identifier $name,
        private readonly array $attributes = [],
        private readonly ?ExpressionNode $value = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::ENUM_CASE);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function value(): ?ExpressionNode
    {
        return $this->value;
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = $this->attributes;

        if ($this->value !== null) {
            $children[] = $this->value;
        }

        return $children;
    }
}
