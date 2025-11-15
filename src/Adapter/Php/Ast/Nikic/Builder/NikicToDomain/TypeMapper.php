<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use PhpParser\Node;
use PhpParser\Node\Name;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;

final class TypeMapper
{
    public function mapQualifiedName(Name $name): Ast\Value\QualifiedName
    {
        $parts = $name->getParts();

        return new Ast\Value\QualifiedName(
            array_map(
                fn (string $part): Ast\Value\Identifier => new Ast\Value\Identifier($part),
                $parts,
            ),
            $name instanceof Name\FullyQualified,
            $name instanceof Name\Relative,
        );
    }

    /**
     * @param string|Node\Identifier|Node\VarLikeIdentifier $identifier
     */
    public function mapIdentifier(string|Node\Identifier|Node\VarLikeIdentifier $identifier): Ast\Value\Identifier
    {
        if (is_string($identifier)) {
            return new Ast\Value\Identifier($identifier);
        }

        return new Ast\Value\Identifier($identifier->toString());
    }

    public function mapType(null|Name|Node\Identifier|Node $type): ?Ast\TypeNode
    {
        if ($type === null) {
            return null;
        }

        if ($type instanceof Node\NullableType) {
            $innerType = $this->mapType($type->type);
            if ($innerType === null) {
                throw new \RuntimeException('Nullable type must have an inner type');
            }
            return new Ast\Type\NullableTypeNode(
                $innerType,
            );
        }

        if ($type instanceof Node\UnionType) {
            return new Ast\Type\UnionTypeNode(
                array_values(array_filter(array_map(
                    fn (Node $inner): ?Ast\TypeNode => $this->mapType($inner),
                    $type->types,
                ))),
            );
        }

        if ($type instanceof Node\IntersectionType) {
            return new Ast\Type\IntersectionTypeNode(
                array_values(array_filter(array_map(
                    fn (Node $inner): ?Ast\TypeNode => $this->mapType($inner),
                    $type->types,
                ))),
            );
        }

        if ($type instanceof Name) {
            return new Ast\Type\NamedTypeNode(
                $this->mapQualifiedName($type),
            );
        }

        if ($type instanceof Node\Identifier) {
            $value = strtolower($type->toString());

            $special = match ($value) {
                'array' => Ast\Type\SpecialType::ARRAY,
                'callable' => Ast\Type\SpecialType::CALLABLE,
                'iterable' => Ast\Type\SpecialType::ITERABLE,
                'void' => Ast\Type\SpecialType::VOID,
                'never' => Ast\Type\SpecialType::NEVER,
                'mixed' => Ast\Type\SpecialType::MIXED,
                'null' => Ast\Type\SpecialType::NULL,
                'false' => Ast\Type\SpecialType::FALSE,
                'true' => Ast\Type\SpecialType::TRUE,
                'object' => Ast\Type\SpecialType::OBJECT,
                'static' => Ast\Type\SpecialType::STATIC,
                'self' => Ast\Type\SpecialType::SELF,
                'parent' => Ast\Type\SpecialType::PARENT,
                default => null,
            };

            if ($special !== null) {
                return new Ast\Type\SpecialTypeNode($special);
            }

            return new Ast\Type\NamedTypeNode(
                new Ast\Value\QualifiedName([new Ast\Value\Identifier($type->toString())]),
            );
        }

        throw new \RuntimeException('Unsupported type: ' . $type::class);
    }
}
