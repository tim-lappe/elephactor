<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Trivia\WhitespaceNode;

final class MemberMapper
{
    public function __construct(
        private readonly ValueMapper $valueMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    /**
     * @param  Node\Stmt[]          $members
     * @return list<Ast\MemberNode>
     */
    public function mapClassMembers(array $members): array
    {
        $result = [];
        $previousEndLine = null;

        foreach ($members as $member) {
            $lineBreaks = $this->calculateLineBreaks($previousEndLine, $member);
            if ($lineBreaks > 0) {
                $result[] = new WhitespaceNode($lineBreaks);
            }

            if ($member instanceof Stmt\Nop) {
                $previousEndLine = $this->resolveEndLine($member);
                continue;
            }

            $result[] = $this->mapMember($member);
            $previousEndLine = $this->resolveEndLine($member);
        }

        return $result;
    }

    private function mapMember(Node\Stmt $member): Ast\MemberNode
    {
        if ($member instanceof Stmt\ClassMethod) {
            return $this->mapMethodDeclaration($member);
        }

        if ($member instanceof Stmt\Property) {
            return $this->mapPropertyDeclaration($member);
        }

        if ($member instanceof Stmt\ClassConst) {
            return $this->mapClassConstantDeclaration($member);
        }

        if ($member instanceof Stmt\TraitUse) {
            return $this->mapTraitUse($member);
        }

        if ($member instanceof Stmt\EnumCase) {
            return $this->mapEnumCase($member);
        }

        throw new \RuntimeException('Unsupported member: ' . $member::class);
    }

    private function mapMethodDeclaration(Stmt\ClassMethod $method): Ast\Declaration\MethodDeclarationNode
    {
        $bodyStatements = $method->stmts !== null ? $this->context->statementMapper()->mapStatements($method->stmts) : [];

        return new Ast\Declaration\MethodDeclarationNode(
            $this->valueMapper->getTypeMapper()->mapIdentifier($method->name),
            $this->valueMapper->mapMethodModifiers($method->flags),
            $this->valueMapper->mapAttributeGroups($method->attrGroups),
            $this->context->expressionMapper()->mapParameters($method->params),
            $bodyStatements,
            $this->valueMapper->getTypeMapper()->mapType($method->returnType),
            $method->byRef,
            $this->valueMapper->mapDocBlock($method->getDocComment()),
        );
    }

    private function mapPropertyDeclaration(Stmt\Property $property): Ast\Declaration\PropertyDeclarationNode
    {
        return new Ast\Declaration\PropertyDeclarationNode(
            $this->valueMapper->mapPropertyModifiers($property->flags),
            array_values(array_map(
                fn (Node\Stmt\PropertyProperty $prop): Ast\Declaration\PropertyNode => $this->mapPropertyNode($prop),
                $property->props,
            )),
            $this->valueMapper->mapAttributeGroups($property->attrGroups),
            $this->valueMapper->getTypeMapper()->mapType($property->type),
            $this->valueMapper->mapDocBlock($property->getDocComment()),
        );
    }

    private function mapPropertyNode(Node\Stmt\PropertyProperty $property): Ast\Declaration\PropertyNode
    {
        return new Ast\Declaration\PropertyNode(
            $this->valueMapper->getTypeMapper()->mapIdentifier($property->name),
            $property->default !== null ? $this->context->expressionMapper()->mapExpression($property->default) : null,
        );
    }

    private function mapClassConstantDeclaration(Stmt\ClassConst $const): Ast\Declaration\ClassConstantDeclarationNode
    {
        return new Ast\Declaration\ClassConstantDeclarationNode(
            $this->valueMapper->mapVisibility($const->flags),
            array_values(array_map(
                fn (Node\Const_ $constElement): Ast\Declaration\ConstElementNode => new Ast\Declaration\ConstElementNode(
                    $this->valueMapper->getTypeMapper()->mapIdentifier($constElement->name),
                    $this->context->expressionMapper()->mapExpression($constElement->value),
                ),
                $const->consts,
            )),
            $this->valueMapper->mapAttributeGroups($const->attrGroups),
            ($const->flags & Stmt\Class_::MODIFIER_FINAL) === Stmt\Class_::MODIFIER_FINAL,
            $this->valueMapper->getTypeMapper()->mapType($const->type),
            $this->valueMapper->mapDocBlock($const->getDocComment()),
        );
    }

    private function mapEnumCase(Stmt\EnumCase $case): Ast\Declaration\EnumCaseNode
    {
        return new Ast\Declaration\EnumCaseNode(
            $this->valueMapper->getTypeMapper()->mapIdentifier($case->name),
            $this->valueMapper->mapAttributeGroups($case->attrGroups),
            $case->expr !== null ? $this->context->expressionMapper()->mapExpression($case->expr) : null,
            $this->valueMapper->mapDocBlock($case->getDocComment()),
        );
    }

    private function mapTraitUse(Stmt\TraitUse $use): Ast\Declaration\TraitUseNode
    {
        return new Ast\Declaration\TraitUseNode(
            array_values(array_map(
                fn (Name $traitName): Ast\Value\QualifiedName => $this->valueMapper->getTypeMapper()->mapQualifiedName($traitName),
                $use->traits,
            )),
            $this->mapTraitAdaptations($use->adaptations),
        );
    }

    /**
     * @param  array<Node\Stmt\TraitUseAdaptation>|null $adaptations
     * @return list<Ast\UseTrait\TraitAdaptationNode>
     */
    private function mapTraitAdaptations(?array $adaptations): array
    {
        if ($adaptations === null) {
            return [];
        }

        return array_values(array_map(
            function (Node\Stmt\TraitUseAdaptation $adaptation): Ast\UseTrait\TraitAdaptationNode {
                if ($adaptation instanceof Stmt\TraitUseAdaptation\Alias) {
                    return $this->mapTraitAliasAdaptation($adaptation);
                }

                if ($adaptation instanceof Stmt\TraitUseAdaptation\Precedence) {
                    return $this->mapTraitPrecedenceAdaptation($adaptation);
                }

                throw new \RuntimeException('Unsupported trait adaptation: ' . $adaptation::class);
            },
            $adaptations,
        ));
    }

    private function mapTraitAliasAdaptation(Stmt\TraitUseAdaptation\Alias $alias): Ast\UseTrait\TraitAliasAdaptationNode
    {
        return new Ast\UseTrait\TraitAliasAdaptationNode(
            $this->valueMapper->getTypeMapper()->mapIdentifier($alias->method),
            $alias->newName !== null ? $this->valueMapper->getTypeMapper()->mapIdentifier($alias->newName) : null,
            $alias->newModifier !== null ? $this->valueMapper->mapVisibility($alias->newModifier) : null,
            $alias->trait !== null ? $this->valueMapper->getTypeMapper()->mapQualifiedName($alias->trait) : null,
        );
    }

    private function mapTraitPrecedenceAdaptation(Stmt\TraitUseAdaptation\Precedence $precedence): Ast\UseTrait\TraitPrecedenceAdaptationNode
    {
        if ($precedence->trait === null) {
            throw new \RuntimeException('Unsupported trait precedence: ' . $precedence::class);
        }

        return new Ast\UseTrait\TraitPrecedenceAdaptationNode(
            $this->valueMapper->getTypeMapper()->mapQualifiedName($precedence->trait),
            $this->valueMapper->getTypeMapper()->mapIdentifier($precedence->method),
            array_values(array_map(
                fn (Name $name): Ast\Value\QualifiedName => $this->valueMapper->getTypeMapper()->mapQualifiedName($name),
                $precedence->insteadof,
            )),
        );
    }

    private function calculateLineBreaks(?int $previousEndLine, Node\Stmt $current): int
    {
        $startLine = $this->resolveStartLine($current);
        if ($previousEndLine === null || $startLine === null) {
            return 0;
        }

        $lineBreaks = $startLine - $previousEndLine - 1;

        return $lineBreaks > 0 ? $lineBreaks : 0;
    }

    private function resolveStartLine(Node $node): ?int
    {
        $line = $node->getStartLine();

        return $line > 0 ? $line : null;
    }

    private function resolveEndLine(Node $node): ?int
    {
        $line = $node->getEndLine();

        return $line > 0 ? $line : null;
    }
}
