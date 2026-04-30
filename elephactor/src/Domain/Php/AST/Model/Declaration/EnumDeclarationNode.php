<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class EnumDeclarationNode extends AbstractNode implements ClassLikeNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $implements
     * @param list<MemberNode>         $members
     */
    public function __construct(
        Identifier $name,
        array $attributes,
        array $implements,
        array $members,
        ?TypeNode $scalarType = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct();

        $name = new IdentifierNode($name);
        $implements = array_map(
            fn (QualifiedName $implementation): QualifiedNameNode => new QualifiedNameNode($implementation),
            $implements,
        );

        $this->children()->add("name", $name);

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }

        foreach ($implements as $implementation) {
            $this->children()->add("implementation", $implementation);
        }

        foreach ($members as $member) {
            $this->children()->add("member", $member);
        }

        if ($scalarType !== null) {
            $this->children()->add("scalarType", $scalarType);
        }
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne("name", IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->children()->getAllOf("attribute", AttributeGroupNode::class);
    }

    /**
     * @return list<QualifiedNameNode>
     */
    public function implements(): array
    {
        return $this->children()->getAllOf("implementation", QualifiedNameNode::class);
    }

    /**
     * @return list<MemberNode>
     */
    public function members(): array
    {
        return $this->children()->getAllOf("member", MemberNode::class);
    }

    public function scalarType(): ?TypeNode
    {
        return $this->children()->getOne("scalarType", TypeNode::class);
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }
}
