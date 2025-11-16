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
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class InterfaceDeclarationNode extends AbstractNode implements ClassLikeNode
{
    private IdentifierNode $name;

    /**
     * @var list<QualifiedNameNode>
     */
    private readonly array $extends;
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $extends
     * @param list<MemberNode>         $members
     */
    public function __construct(
        Identifier $name,
        private readonly array $attributes,
        array $extends,
        private readonly array $members,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::INTERFACE_DECLARATION);

        $this->name = new IdentifierNode($name, $this);
        $this->extends = array_map(
            fn (QualifiedName $extend): QualifiedNameNode => new QualifiedNameNode($extend, $this),
            $extends,
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
    public function extends(): array
    {
        return $this->extends;
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
            $this->name,
            ...$this->attributes,
            ...$this->extends,
            ...$this->members,
        ];
    }
}
