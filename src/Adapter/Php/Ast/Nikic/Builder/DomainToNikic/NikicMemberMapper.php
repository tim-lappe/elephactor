<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Trivia\WhitespaceNode;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\WhitespaceAttribute;

final class NikicMemberMapper implements MemberMapper
{
    public function __construct(
        private readonly ExpressionMapper $expressionMapper,
        private readonly ValueMapper $valueMapper,
        private readonly TypeMapper $typeMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    /**
     * @param  list<Ast\MemberNode> $members
     * @return list<Stmt>
     */
    public function buildMembers(array $members): array
    {
        $result = [];

        foreach ($members as $member) {
            if ($member instanceof WhitespaceNode) {
                $result[] = $this->buildWhitespaceStatement($member);
                continue;
            }

            $result[] = $this->buildMember($member);
        }

        return $result;
    }

    private function buildMember(Ast\MemberNode $member): Stmt
    {
        return match (true) {
            $member instanceof Ast\Declaration\MethodDeclarationNode => $this->buildMethod($member),
            $member instanceof Ast\Declaration\PropertyDeclarationNode => $this->buildProperty($member),
            $member instanceof Ast\Declaration\ClassConstantDeclarationNode => $this->buildClassConstant($member),
            $member instanceof Ast\Declaration\TraitUseNode => $this->buildTraitUse($member),
            $member instanceof Ast\Declaration\EnumCaseNode => $this->buildEnumCase($member),
            default => throw new \RuntimeException('Unsupported member: ' . $member::class),
        };
    }

    private function buildMethod(Ast\Declaration\MethodDeclarationNode $method): Stmt\ClassMethod
    {
        $stmts = $method->modifiers()->isAbstract()
            ? null
            : $this->context->statementMapper()->buildStatements($method->bodyStatements());

        $node = new Stmt\ClassMethod(
            $this->valueMapper->buildIdentifier($method->name()),
            [
                'flags' => $this->valueMapper->buildMethodFlags($method->modifiers()),
                'byRef' => $method->returnsByReference(),
                'params' => $this->expressionMapper->buildParameters($method->parameters()),
                'stmts' => $stmts,
                'returnType' => $this->typeMapper->buildType($method->returnType()),
                'attrGroups' => $this->valueMapper->buildAttributeGroups($method->attributes()),
            ],
        );

        $this->setDocComment($node, $method->docBlock());

        return $node;
    }

    private function buildProperty(Ast\Declaration\PropertyDeclarationNode $property): Stmt\Property
    {
        $props = array_map(
            fn (Ast\Declaration\PropertyNode $prop): Stmt\PropertyProperty => new Stmt\PropertyProperty(
                $this->valueMapper->buildVarLikeIdentifier($prop->name()),
                $prop->defaultValue() !== null
                    ? $this->expressionMapper->buildExpression($prop->defaultValue())
                    : null,
            ),
            $property->properties(),
        );

        $node = new Stmt\Property(
            $this->valueMapper->buildPropertyFlags($property->modifiers()),
            $props,
            [],
            $this->typeMapper->buildType($property->type()),
            $this->valueMapper->buildAttributeGroups($property->attributes()),
        );

        $this->setDocComment($node, $property->docBlock());

        return $node;
    }

    private function buildClassConstant(Ast\Declaration\ClassConstantDeclarationNode $const): Stmt\ClassConst
    {
        $consts = array_map(
            fn (Ast\Declaration\ConstElementNode $element): Const_ => new Const_(
                $this->valueMapper->buildIdentifier($element->name()),
                $this->expressionMapper->buildExpression($element->value()),
            ),
            $const->elements(),
        );

        $flags = $this->valueMapper->buildVisibilityFlag($const->visibility());
        if ($const->isFinal()) {
            $flags |= Stmt\Class_::MODIFIER_FINAL;
        }

        $node = new Stmt\ClassConst(
            $consts,
            $flags,
            [],
            $this->valueMapper->buildAttributeGroups($const->attributes()),
            $this->typeMapper->buildType($const->type()),
        );

        $this->setDocComment($node, $const->docBlock());

        return $node;
    }

    private function buildTraitUse(Ast\Declaration\TraitUseNode $traitUse): Stmt\TraitUse
    {
        $traits = array_map(
            fn (Ast\Value\QualifiedName $name): Node\Name => $this->valueMapper->buildQualifiedName($name),
            $traitUse->traits(),
        );

        return new Stmt\TraitUse(
            $traits,
            $this->buildTraitAdaptations($traitUse->adaptations()),
        );
    }

    /**
     * @param  list<Ast\UseTrait\TraitAdaptationNode> $adaptations
     * @return list<Stmt\TraitUseAdaptation>
     */
    private function buildTraitAdaptations(array $adaptations): array
    {
        return array_map(
            function (Ast\UseTrait\TraitAdaptationNode $adaptation): Stmt\TraitUseAdaptation {
                if ($adaptation instanceof Ast\UseTrait\TraitAliasAdaptationNode) {
                    return $this->buildTraitAliasAdaptation($adaptation);
                }

                if ($adaptation instanceof Ast\UseTrait\TraitPrecedenceAdaptationNode) {
                    return $this->buildTraitPrecedenceAdaptation($adaptation);
                }

                throw new \RuntimeException('Unsupported trait adaptation: ' . $adaptation::class);
            },
            $adaptations,
        );
    }

    private function buildTraitAliasAdaptation(Ast\UseTrait\TraitAliasAdaptationNode $alias): Stmt\TraitUseAdaptation\Alias
    {
        $modifier = $alias->visibility() !== null
            ? $this->valueMapper->buildVisibilityFlag($alias->visibility())
            : null;

        return new Stmt\TraitUseAdaptation\Alias(
            $alias->traitName() !== null ? $this->valueMapper->buildQualifiedName($alias->traitName()) : null,
            $this->valueMapper->buildIdentifier($alias->method()),
            $modifier,
            $alias->alias() !== null ? $this->valueMapper->buildIdentifier($alias->alias()) : null,
        );
    }

    private function buildTraitPrecedenceAdaptation(Ast\UseTrait\TraitPrecedenceAdaptationNode $precedence): Stmt\TraitUseAdaptation\Precedence
    {
        return new Stmt\TraitUseAdaptation\Precedence(
            $this->valueMapper->buildQualifiedName($precedence->originatingTrait()),
            $this->valueMapper->buildIdentifier($precedence->method()),
            array_map(
                fn (Ast\Value\QualifiedName $name): Node\Name => $this->valueMapper->buildQualifiedName($name),
                $precedence->insteadOf(),
            ),
        );
    }

    private function buildEnumCase(Ast\Declaration\EnumCaseNode $case): Stmt\EnumCase
    {
        $node = new Stmt\EnumCase(
            $this->valueMapper->buildIdentifier($case->name()),
            $case->value() !== null ? $this->expressionMapper->buildExpression($case->value()) : null,
            $this->valueMapper->buildAttributeGroups($case->attributes()),
        );

        $this->setDocComment($node, $case->docBlock());

        return $node;
    }

    private function setDocComment(Node $node, ?DocBlock $docBlock): void
    {
        $doc = $this->valueMapper->buildDocBlock($docBlock);
        if ($doc !== null) {
            $node->setDocComment($doc);
        }
    }

    private function buildWhitespaceStatement(WhitespaceNode $whitespace): Stmt\Nop
    {
        $nop = new Stmt\Nop();
        WhitespaceAttribute::set($nop, $whitespace->lineBreaks());

        return $nop;
    }
}
