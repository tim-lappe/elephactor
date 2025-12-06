<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class InterfaceDeclarationNode extends AbstractNode implements ClassLikeNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $extends
     * @param list<MemberNode>         $members
     */
    public function __construct(
        Identifier $name,
        array $attributes,
        array $extends,
        array $members,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct();

        $name = new IdentifierNode($name);

        $this->children()->add("name", $name);

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }

        foreach ($extends as $extend) {
            $this->children()->add("extend", new QualifiedNameNode($extend));
        }

        foreach ($members as $member) {
            $this->children()->add("member", $member);
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
    public function extends(): array
    {
        return $this->children()->getAllOf("extend", QualifiedNameNode::class);
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
