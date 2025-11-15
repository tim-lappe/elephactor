<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\MemberNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\ClassModifiers;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class AnonymousClassExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode>       $constructorArguments
     * @param list<AttributeGroupNode> $attributes
     * @param list<QualifiedName>      $interfaces
     * @param list<MemberNode>         $members
     */
    public function __construct(
        private readonly array $constructorArguments,
        private readonly array $attributes,
        private readonly array $interfaces,
        private readonly array $members,
        private readonly ClassModifiers $modifiers = new ClassModifiers(),
        private readonly ?QualifiedName $extends = null
    ) {
        parent::__construct(NodeKind::ANONYMOUS_CLASS_EXPRESSION);
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

    public function modifiers(): ClassModifiers
    {
        return $this->modifiers;
    }

    public function extends(): ?QualifiedName
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
