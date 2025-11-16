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
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class EnumDeclarationNode extends AbstractNode implements ClassLikeNode
{
    private IdentifierNode $name;

    /**
     * @var list<QualifiedNameNode>
     */
    private readonly array $implements;
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $implements
     * @param list<MemberNode>         $members
     */
    public function __construct(
        Identifier $name,
        private readonly array $attributes,
        array $implements,
        private readonly array $members,
        private readonly ?TypeNode $scalarType = null,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::ENUM_DECLARATION);

        $this->name = new IdentifierNode($name, $this);
        $this->implements = array_map(
            fn (QualifiedName $implementation): QualifiedNameNode => new QualifiedNameNode($implementation, $this),
            $implements,
        );
    }

    public function name(): IdentifierNode
    {
        return $this->name;
    }

    public function changeName(Identifier $name): void
    {
        $this->name->changeIdentifier($name);
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return list<QualifiedNameNode>
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
            $this->name,
            ...$this->attributes,
            ...$this->implements,
            ...$this->members,
        ];

        if ($this->scalarType !== null) {
            $children[] = $this->scalarType;
        }

        return $children;
    }
}
