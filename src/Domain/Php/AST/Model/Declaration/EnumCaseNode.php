<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class EnumCaseNode extends AbstractNode implements MemberNode
{
    private IdentifierNode $name;
    /**
     * @param list<AttributeGroupNode> $attributes
     */
    public function __construct(
        Identifier $name,
        private readonly array $attributes = [],
        private readonly ?ExpressionNode $value = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::ENUM_CASE);

        $this->name = new IdentifierNode($name, $this);
    }

    public function name(): IdentifierNode
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
        $children = [
            $this->name,
            ...$this->attributes,
        ];

        if ($this->value !== null) {
            $children[] = $this->value;
        }

        return $children;
    }
}
