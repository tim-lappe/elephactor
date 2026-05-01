<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;
use TimLappe\Elephactor\Domain\Php\AST\Model\Type\NamedTypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Type\NullableTypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Type\SpecialTypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Type\UnionTypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class TypeMapper
{
    public function buildType(?Ast\TypeNode $type): Identifier|Name|ComplexType|null
    {
        if ($type === null) {
            return null;
        }

        if ($type instanceof NullableTypeNode) {
            $inner = $this->buildType($type->inner());
            if (!$inner instanceof Identifier && !$inner instanceof Name) {
                throw new \RuntimeException('Nullable type requires an inner named type');
            }

            $node = $type->getAdapterNode();
            if (!$node instanceof NullableType) {
                $node = new NullableType($inner);
            }

            $node->type = $inner;
            $type->setAdapterNode($node);

            return $node;
        }

        if ($type instanceof UnionTypeNode) {
            $types = array_map(
                function (Ast\TypeNode $node): Identifier|Name|IntersectionType {
                    $built = $this->buildType($node);
                    if ($built instanceof Identifier || $built instanceof Name || $built instanceof IntersectionType) {
                        return $built;
                    }

                    throw new \RuntimeException('Unsupported union type segment');
                },
                $type->types(),
            );

            $node = $type->getAdapterNode();
            if (!$node instanceof UnionType) {
                $node = new UnionType($types);
            }

            $node->types = $types;
            $type->setAdapterNode($node);

            return $node;
        }

        if ($type instanceof Ast\Type\IntersectionTypeNode) {
            $types = array_map(
                function (Ast\TypeNode $node): Identifier|Name {
                    $built = $this->buildType($node);
                    if ($built instanceof Identifier || $built instanceof Name) {
                        return $built;
                    }

                    throw new \RuntimeException('Unsupported intersection type segment');
                },
                $type->types(),
            );

            $node = $type->getAdapterNode();
            if (!$node instanceof IntersectionType) {
                $node = new IntersectionType($types);
            }

            $node->types = $types;
            $type->setAdapterNode($node);

            return $node;
        }

        if ($type instanceof NamedTypeNode) {
            $built = $this->buildQualifiedName($type->name()->qualifiedName());
            $node = $type->getAdapterNode();

            if ($node instanceof Name) {
                if ($node::class === $built::class) {
                    $node->name = $built->name;
                } else {
                    $comments = $node->getComments();
                    if ($comments !== []) {
                        $built->setAttribute('comments', $comments);
                    }
                    $node = $built;
                }
            } elseif ($node instanceof Identifier) {
                $node->name = $built->toString();
            } else {
                $node = $built;
            }

            $type->setAdapterNode($node);

            return $node;
        }

        if ($type instanceof SpecialTypeNode) {
            $node = $type->getAdapterNode();
            if (!$node instanceof Identifier) {
                $node = new Identifier($type->type()->value);
            }

            $node->name = $type->type()->value;
            $type->setAdapterNode($node);

            return $node;
        }

        throw new \RuntimeException('Unsupported type node: ' . $type::class);
    }

    public function buildEnumScalarType(?Ast\TypeNode $type): ?Identifier
    {
        $scalarType = $this->buildType($type);
        if ($scalarType === null) {
            return null;
        }

        if (!$scalarType instanceof Identifier) {
            throw new \RuntimeException('Enum scalar type must be string or int');
        }

        return $scalarType;
    }

    public function buildQualifiedName(QualifiedName $name): Name
    {
        $parts = array_map(
            static fn (Ast\Value\Identifier $identifier): string => $identifier->value(),
            $name->parts(),
        );

        if ($name instanceof FullyQualifiedName) {
            return new Name\FullyQualified($parts);
        }

        return new Name($parts);
    }
}
