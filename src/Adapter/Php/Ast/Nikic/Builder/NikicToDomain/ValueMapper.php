<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;

final class ValueMapper
{
    public function __construct(
        private readonly TypeMapper $typeMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    public function getTypeMapper(): TypeMapper
    {
        return $this->typeMapper;
    }

    public function mapVisibility(int $flags): Ast\Value\Visibility
    {
        return match (true) {
            ($flags & Stmt\Class_::MODIFIER_PROTECTED) === Stmt\Class_::MODIFIER_PROTECTED => Ast\Value\Visibility::PROTECTED,
            ($flags & Stmt\Class_::MODIFIER_PRIVATE) === Stmt\Class_::MODIFIER_PRIVATE => Ast\Value\Visibility::PRIVATE,
            default => Ast\Value\Visibility::PUBLIC,
        };
    }

    public function mapClassModifiers(int $flags): Ast\Value\ClassModifiers
    {
        return new Ast\Value\ClassModifiers(
            ($flags & Stmt\Class_::MODIFIER_ABSTRACT) === Stmt\Class_::MODIFIER_ABSTRACT,
            ($flags & Stmt\Class_::MODIFIER_FINAL) === Stmt\Class_::MODIFIER_FINAL,
            ($flags & Stmt\Class_::MODIFIER_READONLY) === Stmt\Class_::MODIFIER_READONLY,
        );
    }

    public function mapMethodModifiers(int $flags): Ast\Value\MethodModifiers
    {
        return new Ast\Value\MethodModifiers(
            $this->mapVisibility($flags),
            ($flags & Stmt\Class_::MODIFIER_STATIC) === Stmt\Class_::MODIFIER_STATIC,
            ($flags & Stmt\Class_::MODIFIER_ABSTRACT) === Stmt\Class_::MODIFIER_ABSTRACT,
            ($flags & Stmt\Class_::MODIFIER_FINAL) === Stmt\Class_::MODIFIER_FINAL,
        );
    }

    public function mapPropertyModifiers(int $flags): Ast\Value\PropertyModifiers
    {
        return new Ast\Value\PropertyModifiers(
            $this->mapVisibility($flags),
            ($flags & Stmt\Class_::MODIFIER_STATIC) === Stmt\Class_::MODIFIER_STATIC,
            ($flags & Stmt\Class_::MODIFIER_READONLY) === Stmt\Class_::MODIFIER_READONLY,
        );
    }

    public function mapDocBlock(?Doc $doc): ?Ast\Value\DocBlock
    {
        if ($doc === null) {
            return null;
        }

        $content = trim($doc->getText());

        if ($content === '') {
            return null;
        }

        return new Ast\Value\DocBlock($content);
    }

    /**
     * @param  Node\AttributeGroup[]                  $groups
     * @return list<Ast\Attribute\AttributeGroupNode>
     */
    public function mapAttributeGroups(array $groups): array
    {
        return array_values(array_map(
            fn (Node\AttributeGroup $group): Ast\Attribute\AttributeGroupNode => $this->mapAttributeGroup($group),
            $groups,
        ));
    }

    private function mapAttributeGroup(Node\AttributeGroup $group): Ast\Attribute\AttributeGroupNode
    {
        return new Ast\Attribute\AttributeGroupNode(
            array_values(array_map(
                fn (Node\Attribute $attribute): Ast\Attribute\AttributeNode => $this->mapAttribute($attribute),
                $group->attrs,
            )),
        );
    }

    private function mapAttribute(Node\Attribute $attribute): Ast\Attribute\AttributeNode
    {
        return new Ast\Attribute\AttributeNode(
            $this->typeMapper->mapQualifiedName($attribute->name),
            array_map(
                fn (Node\Arg $arg): Ast\Attribute\AttributeArgumentNode => $this->mapAttributeArgument($arg),
                $attribute->args,
            ),
        );
    }

    private function mapAttributeArgument(Node\Arg $argument): Ast\Attribute\AttributeArgumentNode
    {
        return new Ast\Attribute\AttributeArgumentNode(
            $this->context->expressionMapper()->mapExpression($argument->value),
            $argument->name !== null ? $this->typeMapper->mapIdentifier($argument->name) : null,
        );
    }

    public function resolveUseKind(int $type): Ast\Statement\UseKind
    {
        return match ($type) {
            Stmt\Use_::TYPE_CONSTANT => Ast\Statement\UseKind::CONSTANT_IMPORT,
            Stmt\Use_::TYPE_FUNCTION => Ast\Statement\UseKind::FUNCTION_IMPORT,
            default => Ast\Statement\UseKind::CLASS_IMPORT,
        };
    }
}
