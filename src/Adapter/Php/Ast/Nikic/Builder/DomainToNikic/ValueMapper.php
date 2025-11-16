<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;

final class ValueMapper
{
    public function __construct(
        private readonly TypeMapper $typeMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    public function typeMapper(): TypeMapper
    {
        return $this->typeMapper;
    }

    public function buildIdentifier(Ast\Value\Identifier $identifier): Identifier
    {
        return new Identifier($identifier->value());
    }

    public function buildVarLikeIdentifier(Ast\Value\Identifier $identifier): VarLikeIdentifier
    {
        return new VarLikeIdentifier($identifier->value());
    }

    public function identifierString(Ast\Value\Identifier $identifier): string
    {
        return $identifier->value();
    }

    public function buildQualifiedName(Ast\Value\QualifiedName $name): Name
    {
        return $this->typeMapper->buildQualifiedName($name);
    }

    public function buildDocBlock(?Ast\Value\DocBlock $docBlock): ?Doc
    {
        if ($docBlock === null) {
            return null;
        }

        return new Doc($docBlock->content());
    }

    /**
     * @param  list<Ast\Attribute\AttributeGroupNode> $groups
     * @return list<AttributeGroup>
     */
    public function buildAttributeGroups(array $groups): array
    {
        return array_map(
            fn (Ast\Attribute\AttributeGroupNode $group): AttributeGroup => $this->buildAttributeGroup($group),
            $groups,
        );
    }

    private function buildAttributeGroup(Ast\Attribute\AttributeGroupNode $group): AttributeGroup
    {
        return new AttributeGroup(
            array_map(
                fn (Ast\Attribute\AttributeNode $attribute): Attribute => $this->buildAttribute($attribute),
                $group->attributes(),
            ),
        );
    }

    private function buildAttribute(Ast\Attribute\AttributeNode $attribute): Attribute
    {
        return new Attribute(
            $this->buildQualifiedName($attribute->name()->qualifiedName()),
            array_map(
                fn (Ast\Attribute\AttributeArgumentNode $argument): Arg => $this->buildAttributeArgument($argument),
                $attribute->arguments(),
            ),
        );
    }

    private function buildAttributeArgument(Ast\Attribute\AttributeArgumentNode $argument): Arg
    {
        $expression = $this->context->expressionMapper()->buildExpression($argument->expression());

        return new Node\Arg(
            $expression,
            false,
            false,
            [],
            $argument->name() !== null ? $this->buildIdentifier($argument->name()->identifier()) : null,
        );
    }

    public function buildClassFlags(Ast\Value\ClassModifiers $modifiers): int
    {
        $flags = 0;

        if ($modifiers->isAbstract()) {
            $flags |= Stmt\Class_::MODIFIER_ABSTRACT;
        }

        if ($modifiers->isFinal()) {
            $flags |= Stmt\Class_::MODIFIER_FINAL;
        }

        if ($modifiers->isReadonly()) {
            $flags |= Stmt\Class_::MODIFIER_READONLY;
        }

        return $flags;
    }

    public function buildMethodFlags(Ast\Value\MethodModifiers $modifiers): int
    {
        $flags = $this->buildVisibilityFlag($modifiers->visibility());

        if ($modifiers->isStatic()) {
            $flags |= Stmt\Class_::MODIFIER_STATIC;
        }

        if ($modifiers->isExplicitAbstract()) {
            $flags |= Stmt\Class_::MODIFIER_ABSTRACT;
        }

        if ($modifiers->isFinal()) {
            $flags |= Stmt\Class_::MODIFIER_FINAL;
        }

        return $flags;
    }

    public function buildPropertyFlags(Ast\Value\PropertyModifiers $modifiers): int
    {
        $flags = $this->buildVisibilityFlag($modifiers->visibility());

        if ($modifiers->isStatic()) {
            $flags |= Stmt\Class_::MODIFIER_STATIC;
        }

        if ($modifiers->isReadonly()) {
            $flags |= Stmt\Class_::MODIFIER_READONLY;
        }

        return $flags;
    }

    public function buildVisibilityFlag(Ast\Value\Visibility $visibility): int
    {
        return match ($visibility) {
            Ast\Value\Visibility::PUBLIC => Stmt\Class_::MODIFIER_PUBLIC,
            Ast\Value\Visibility::PROTECTED => Stmt\Class_::MODIFIER_PROTECTED,
            Ast\Value\Visibility::PRIVATE => Stmt\Class_::MODIFIER_PRIVATE,
        };
    }

    /**
     * @return int<1, 3>
     */
    public function buildUseType(Ast\Statement\UseKind $kind): int
    {
        return match ($kind) {
            Ast\Statement\UseKind::CONSTANT_IMPORT => Stmt\Use_::TYPE_CONSTANT,
            Ast\Statement\UseKind::FUNCTION_IMPORT => Stmt\Use_::TYPE_FUNCTION,
            default => Stmt\Use_::TYPE_NORMAL,
        };
    }
}
