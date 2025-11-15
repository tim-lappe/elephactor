<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\ClassModifiers;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class ClassDeclarationNode extends AbstractNode implements ClassLikeNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $interfaces
     * @param list<MemberNode>         $members
     */
    public function __construct(
        private Identifier $name,
        private readonly ClassModifiers $modifiers,
        private readonly array $attributes,
        private readonly array $interfaces,
        private readonly array $members,
        private readonly ?QualifiedName $extends = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::CLASS_DECLARATION);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    public function changeName(Identifier $name): void
    {
        $this->name = $name;
    }

    public function modifiers(): ClassModifiers
    {
        return $this->modifiers;
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function extends(): ?QualifiedName
    {
        return $this->extends;
    }

    /**
     * @return list<QualifiedName>
     */
    public function interfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * @return list<MemberNode>
     */
    public function members(): array
    {
        return $this->members;
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
        return [
            ...$this->attributes,
            ...$this->members,
        ];
    }
}
