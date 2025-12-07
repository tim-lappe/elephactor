<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\ClassModifiers;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;

final class AnonymousClassExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode>       $constructorArguments
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $interfaces
     * @param list<MemberNode>         $members
     */
    public function __construct(
        array $constructorArguments,
        array $attributes,
        array $interfaces,
        array $members,
        private readonly ClassModifiers $modifiers = new ClassModifiers(),
        ?QualifiedName $extends = null
    ) {
        parent::__construct();

        $extendsNode = $extends !== null ? new QualifiedNameNode($extends) : null;
        $interfaceNodes = array_map(
            static fn (QualifiedName $interface): QualifiedNameNode => new QualifiedNameNode($interface),
            $interfaces
        );

        foreach ($attributes as $attribute) {
            $this->children()->add('attribute', $attribute);
        }

        foreach ($constructorArguments as $constructorArgument) {
            $this->children()->add('constructorArgument', $constructorArgument);
        }

        foreach ($members as $member) {
            $this->children()->add('member', $member);
        }

        foreach ($interfaceNodes as $interface) {
            $this->children()->add('interface', $interface);
        }

        if ($extendsNode !== null) {
            $this->children()->add('extends', $extendsNode);
        }
    }

    /**
     * @return list<ArgumentNode>
     */
    public function constructorArguments(): array
    {
        return $this->children()->getAllOf('constructorArgument', ArgumentNode::class);
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->children()->getAllOf('attribute', AttributeGroupNode::class);
    }

    /**
     * @return list<QualifiedNameNode>
     */
    public function interfaces(): array
    {
        return $this->children()->getAllOf('interface', QualifiedNameNode::class);
    }

    /**
     * @return list<MemberNode>
     */
    public function members(): array
    {
        return $this->children()->getAllOf('member', MemberNode::class);
    }

    public function modifiers(): ClassModifiers
    {
        return $this->modifiers;
    }

    public function extends(): ?QualifiedNameNode
    {
        return $this->children()->getOne('extends', QualifiedNameNode::class);
    }
}
