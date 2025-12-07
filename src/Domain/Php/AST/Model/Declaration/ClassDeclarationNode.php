<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\ClassModifiers;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class ClassDeclarationNode extends AbstractNode implements ClassLikeNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $interfaces
     * @param list<MemberNode>         $members
     */
    public function __construct(
        Identifier $name,
        private readonly ClassModifiers $modifiers,
        array $attributes,
        array $interfaces,
        array $members,
        ?QualifiedName $extends = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct();

        $name = new IdentifierNode($name);
        $interfaces = array_map(
            fn (QualifiedName $interface): QualifiedNameNode => new QualifiedNameNode($interface),
            $interfaces,
        );

        $this->children()->add("name", $name);

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }

        foreach ($members as $member) {
            $this->children()->add("member", $member);
        }

        foreach ($interfaces as $interface) {
            $this->children()->add("interface", $interface);
        }

        if ($extends !== null) {
            $this->children()->add("extends", new QualifiedNameNode($extends));
        }
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne("name", IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
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
        return $this->children()->getAllOf("attribute", AttributeGroupNode::class);
    }

    public function extends(): ?QualifiedNameNode
    {
        return $this->children()->getOne("extends", QualifiedNameNode::class);
    }

    /**
     * @return list<QualifiedNameNode>
     */
    public function interfaces(): array
    {
        return $this->children()->getAllOf("interface", QualifiedNameNode::class);
    }

    /**
     * @return list<MemberNode>
     */
    public function members(): array
    {
        return $this->children()->getAllOf("member", MemberNode::class);
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }
}
