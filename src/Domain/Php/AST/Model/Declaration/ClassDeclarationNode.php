<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\ClassModifiers;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class ClassDeclarationNode extends AbstractNode implements ClassLikeNode
{
    private IdentifierNode $name;

    /**
     * @var list<QualifiedNameNode>
     */
    private readonly array $interfaces;

    private readonly ?QualifiedNameNode $extends;
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $interfaces
     * @param list<MemberNode>         $members
     */
    public function __construct(
        Identifier $name,
        private readonly ClassModifiers $modifiers,
        private readonly array $attributes,
        array $interfaces,
        private readonly array $members,
        ?QualifiedName $extends = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::CLASS_DECLARATION);

        $this->name = new IdentifierNode($name, $this);
        $this->interfaces = array_map(
            fn (QualifiedName $interface): QualifiedNameNode => new QualifiedNameNode($interface, $this),
            $interfaces,
        );
        $this->extends = $extends !== null ? new QualifiedNameNode($extends, $this) : null;
    }

    public function name(): IdentifierNode
    {
        return $this->name;
    }

    public function changeName(Identifier $name): void
    {
        $this->name->changeIdentifier($name);
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

    public function extends(): ?QualifiedNameNode
    {
        return $this->extends;
    }

    /**
     * @return list<QualifiedNameNode>
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
        $children = [
            $this->name,
            ...$this->attributes,
            ...$this->members,
            ...$this->interfaces,
        ];

        if ($this->extends !== null) {
            $children[] = $this->extends;
        }

        return $children;
    }
}
