<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\ClassModifiers;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;

final class AnonymousClassExpressionNode extends AbstractNode implements ExpressionNode
{
    private ?QualifiedNameNode $extends = null;
    /**
     * @var list<QualifiedNameNode>
     */
    private readonly array $interfaces;

    /**
     * @param list<ArgumentNode>       $constructorArguments
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $interfaces
     * @param list<MemberNode>         $members
     */
    public function __construct(
        private readonly array $constructorArguments,
        private readonly array $attributes,
        array $interfaces,
        private readonly array $members,
        private readonly ClassModifiers $modifiers = new ClassModifiers(),
        ?QualifiedName $extends = null
    ) {
        parent::__construct(NodeKind::ANONYMOUS_CLASS_EXPRESSION);

        $this->extends = $extends !== null ? new QualifiedNameNode($extends, $this) : null;
        $this->interfaces = array_map(
            fn (QualifiedName $interface): QualifiedNameNode => new QualifiedNameNode($interface, $this),
            $interfaces,
        );
    }

    /**
     * @return list<ArgumentNode>
     */
    public function constructorArguments(): array
    {
        return $this->constructorArguments;
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

    public function modifiers(): ClassModifiers
    {
        return $this->modifiers;
    }

    public function extends(): ?QualifiedNameNode
    {
        return $this->extends;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            ...$this->attributes,
            ...$this->constructorArguments,
            ...$this->members,
        ];
    }
}
