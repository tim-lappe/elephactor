<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class EnumDeclarationNode extends AbstractNode implements ClassLikeNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $implements
     * @param list<MemberNode>         $members
     */
    public function __construct(
        private Identifier $name,
        private readonly array $attributes,
        private readonly array $implements,
        private readonly array $members,
        private readonly ?TypeNode $scalarType = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::ENUM_DECLARATION);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    public function changeName(Identifier $name): void
    {
        $this->name = $name;
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return list<QualifiedName>
     */
    public function implements(): array
    {
        return $this->implements;
    }

    /**
     * @return list<MemberNode>
     */
    public function members(): array
    {
        return $this->members;
    }

    public function scalarType(): ?TypeNode
    {
        return $this->scalarType;
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
            ...$this->attributes,
            ...$this->members,
        ];

        if ($this->scalarType !== null) {
            $children[] = $this->scalarType;
        }

        return $children;
    }
}
