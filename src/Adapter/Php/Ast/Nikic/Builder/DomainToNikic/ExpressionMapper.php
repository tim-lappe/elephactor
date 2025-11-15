<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\ClosureUse;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\ArrayItemNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\ClosureUseVariableNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\ListItemNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression\MatchArmNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\AssignmentOperator;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\BinaryOperator;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\EncapsedStringKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\IncludeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\LiteralKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\LiteralValue;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\ParameterPassingMode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\UnaryOperator;

final class ExpressionMapper
{
    public function __construct(
        private readonly ValueMapper $valueMapper,
        private readonly TypeMapper $typeMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    public function buildExpression(ExpressionNode $expression): Expr
    {
        return match (true) {
            $expression instanceof Ast\Expression\LiteralExpressionNode => $this->buildLiteralExpression($expression->value()),
            $expression instanceof Ast\Expression\VariableExpressionNode => $this->buildVariableExpression($expression),
            $expression instanceof Ast\Expression\ConstantFetchExpressionNode => new Expr\ConstFetch($this->valueMapper->buildQualifiedName($expression->name())),
            $expression instanceof Ast\Expression\ClassConstantFetchExpressionNode => $this->buildClassConstFetch($expression),
            $expression instanceof Ast\Expression\PropertyFetchExpressionNode => $this->buildPropertyFetch($expression),
            $expression instanceof Ast\Expression\StaticPropertyFetchExpressionNode => $this->buildStaticPropertyFetch($expression),
            $expression instanceof Ast\Expression\ArrayAccessExpressionNode => $this->buildArrayDimFetch($expression),
            $expression instanceof Ast\Expression\ArrayExpressionNode => $this->buildArrayExpression($expression),
            $expression instanceof Ast\Expression\FunctionCallExpressionNode => $this->buildFunctionCall($expression),
            $expression instanceof Ast\Expression\MethodCallExpressionNode => $this->buildMethodCall($expression),
            $expression instanceof Ast\Expression\StaticCallExpressionNode => $this->buildStaticCall($expression),
            $expression instanceof Ast\Expression\NewExpressionNode => $this->buildNewExpression($expression),
            $expression instanceof Ast\Expression\AnonymousClassExpressionNode => $this->buildAnonymousClassExpression($expression),
            $expression instanceof Ast\Expression\AssignmentExpressionNode => new Expr\Assign(
                $this->buildExpression($expression->target()),
                $this->buildExpression($expression->value()),
            ),
            $expression instanceof Ast\Expression\CompoundAssignmentExpressionNode => $this->buildCompoundAssignment($expression),
            $expression instanceof Ast\Expression\BinaryExpressionNode => $this->buildBinaryExpression($expression),
            $expression instanceof Ast\Expression\UnaryExpressionNode => $this->buildUnaryExpression($expression),
            $expression instanceof Ast\Expression\TernaryExpressionNode => new Expr\Ternary(
                $this->buildExpression($expression->condition()),
                $expression->ifTrue() !== null ? $this->buildExpression($expression->ifTrue()) : null,
                $this->buildExpression($expression->ifFalse()),
            ),
            $expression instanceof Ast\Expression\ErrorSuppressExpressionNode => new Expr\ErrorSuppress(
                $this->buildExpression($expression->expression()),
            ),
            $expression instanceof Ast\Expression\CloneExpressionNode => new Expr\Clone_(
                $this->buildExpression($expression->expression()),
            ),
            $expression instanceof Ast\Expression\IncludeExpressionNode => new Expr\Include_(
                $this->buildExpression($expression->path()),
                $this->mapIncludeKind($expression->includeKind()),
            ),
            $expression instanceof Ast\Expression\EvalExpressionNode => new Expr\Eval_(
                $this->buildExpression($expression->code()),
            ),
            $expression instanceof Ast\Expression\PrintExpressionNode => new Expr\Print_(
                $this->buildExpression($expression->expression()),
            ),
            $expression instanceof Ast\Expression\ShellCommandExpressionNode => new Expr\ShellExec(
                $this->buildShellParts($expression->parts()),
            ),
            $expression instanceof Ast\Expression\ExitExpressionNode => $this->buildExitExpression($expression),
            $expression instanceof Ast\Expression\EmptyExpressionNode => new Expr\Empty_(
                $this->buildExpression($expression->expression()),
            ),
            $expression instanceof Ast\Expression\IssetExpressionNode => new Expr\Isset_(
                array_map(
                    fn (ExpressionNode $expr): Expr => $this->buildExpression($expr),
                    $expression->expressions(),
                ),
            ),
            $expression instanceof Ast\Expression\InstanceofExpressionNode => $this->buildInstanceof($expression),
            $expression instanceof Ast\Expression\CastExpressionNode => $this->buildCastExpression($expression),
            $expression instanceof Ast\Expression\EncapsedStringExpressionNode => $this->buildEncapsedString($expression),
            $expression instanceof Ast\Expression\ClosureExpressionNode => $this->buildClosureExpression($expression),
            $expression instanceof Ast\Expression\ArrowFunctionExpressionNode => $this->buildArrowFunction($expression),
            $expression instanceof Ast\Expression\MatchExpressionNode => $this->buildMatchExpression($expression),
            $expression instanceof Ast\Expression\ThrowExpressionNode => new Expr\Throw_(
                $this->buildExpression($expression->expression()),
            ),
            $expression instanceof Ast\Expression\YieldExpressionNode => $this->buildYieldExpression($expression),
            $expression instanceof Ast\Expression\YieldFromExpressionNode => new Expr\YieldFrom(
                $this->buildExpression($expression->expression()),
            ),
            $expression instanceof Ast\Expression\ListExpressionNode => $this->buildListExpression($expression),
            default => throw new \RuntimeException('Unsupported expression: ' . $expression::class),
        };
    }

    /**
     * @param  list<ArgumentNode> $arguments
     * @return list<Arg>
     */
    public function buildArguments(array $arguments): array
    {
        return array_map(
            fn (ArgumentNode $argument): Arg => new Arg(
                $this->buildExpression($argument->expression()),
                false,
                $argument->isUnpacked(),
                [],
                $argument->name() !== null ? $this->valueMapper->buildIdentifier($argument->name()) : null,
            ),
            $arguments,
        );
    }

    /**
     * @param  list<ParameterNode> $parameters
     * @return list<Param>
     */
    public function buildParameters(array $parameters): array
    {
        return array_map(
            fn (ParameterNode $parameter): Param => $this->buildParameter($parameter),
            $parameters,
        );
    }

    private function buildParameter(ParameterNode $parameter): Param
    {
        $flags = 0;

        if ($parameter->promotedVisibility() !== null) {
            $flags |= $this->valueMapper->buildVisibilityFlag($parameter->promotedVisibility());
        }

        if ($parameter->isPromotedReadonly()) {
            $flags |= Stmt\Class_::MODIFIER_READONLY;
        }

        return new Param(
            var: new Expr\Variable($parameter->name()->value()),
            default: $parameter->defaultValue() !== null ? $this->buildExpression($parameter->defaultValue()) : null,
            type: $this->typeMapper->buildType($parameter->type()),
            byRef: $parameter->passingMode() === ParameterPassingMode::BY_REFERENCE,
            variadic: $parameter->isVariadic(),
            attrGroups: $this->valueMapper->buildAttributeGroups($parameter->attributes()),
            flags: $flags,
        );
    }

    /**
     * @param  list<ClosureUseVariableNode> $uses
     * @return list<ClosureUse>
     */
    private function buildClosureUses(array $uses): array
    {
        return array_map(
            fn (ClosureUseVariableNode $use): ClosureUse => new ClosureUse(
                new Expr\Variable($use->name()->value()),
                $use->byReference(),
            ),
            $uses,
        );
    }

    /**
     * @param  list<ArrayItemNode>  $items
     * @return list<Node\ArrayItem>
     */
    private function buildArrayItems(array $items): array
    {
        return array_map(
            fn (ArrayItemNode $item): Node\ArrayItem => new Node\ArrayItem(
                $this->buildExpression($item->value()),
                $item->key() !== null ? $this->buildExpression($item->key()) : null,
                $item->byReference(),
                [],
                $item->isUnpacked(),
            ),
            $items,
        );
    }

    /**
     * @param  list<ListItemNode>        $items
     * @return list<Node\ArrayItem|null>
     */
    private function buildListItems(array $items): array
    {
        return array_map(
            fn (ListItemNode $item): Node\ArrayItem => new Node\ArrayItem(
                $this->buildExpression($item->value()),
                $item->key() !== null ? $this->buildExpression($item->key()) : null,
            ),
            $items,
        );
    }

    /**
     * @param  list<MatchArmNode> $arms
     * @return list<MatchArm>
     */
    private function buildMatchArms(array $arms): array
    {
        return array_map(
            function (MatchArmNode $arm): MatchArm {
                $conds = $arm->isDefault()
                    ? []
                    : array_map(
                        fn (ExpressionNode $expression): Expr => $this->buildExpression($expression),
                        $arm->conditions(),
                    );

                return new MatchArm(
                    $conds,
                    $this->buildExpression($arm->body()),
                );
            },
            $arms,
        );
    }

    private function buildLiteralExpression(LiteralValue $value): Expr
    {
        return match ($value->kind()) {
            LiteralKind::STRING => new Scalar\String_($this->literalStringValue($value)),
            LiteralKind::INTEGER => new Scalar\LNumber($this->literalIntegerValue($value)),
            LiteralKind::FLOAT => new Scalar\DNumber($this->literalFloatValue($value)),
            LiteralKind::BOOLEAN => new Expr\ConstFetch(new Name($this->literalBooleanValue($value) ? 'true' : 'false')),
            LiteralKind::NULL => new Expr\ConstFetch(new Name('null')),
            LiteralKind::ARRAY => new Expr\Array_(
                $this->buildLiteralArray($this->literalArrayValue($value)),
            ),
        };
    }

    /**
     * @param  array<array-key, mixed> $items
     * @return list<Node\ArrayItem>
     */
    private function buildLiteralArray(array $items): array
    {
        $result = [];
        $isList = array_is_list($items);

        foreach ($items as $key => $item) {
            $expr = $this->buildLiteralExpression($this->wrapLiteral($item));
            $keyExpr = $isList
                ? null
                : $this->buildLiteralExpression($this->wrapLiteral($key));

            $result[] = new Node\ArrayItem($expr, $keyExpr);
        }

        return $result;
    }

    private function wrapLiteral(mixed $value): LiteralValue
    {
        return match (true) {
            is_string($value) => LiteralValue::string($value),
            is_int($value) => LiteralValue::integer($value),
            is_float($value) => LiteralValue::float($value),
            is_bool($value) => LiteralValue::boolean($value),
            $value === null => LiteralValue::null(),
            is_array($value) => LiteralValue::array($value),
            default => throw new \RuntimeException('Unsupported literal value'),
        };
    }

    private function literalStringValue(LiteralValue $value): string
    {
        $raw = $value->value();
        if (!is_string($raw)) {
            throw new \LogicException('Expected string literal value');
        }

        return $raw;
    }

    private function literalIntegerValue(LiteralValue $value): int
    {
        $raw = $value->value();
        if (!is_int($raw)) {
            throw new \LogicException('Expected integer literal value');
        }

        return $raw;
    }

    private function literalFloatValue(LiteralValue $value): float
    {
        $raw = $value->value();
        if (!is_float($raw)) {
            throw new \LogicException('Expected float literal value');
        }

        return $raw;
    }

    private function literalBooleanValue(LiteralValue $value): bool
    {
        $raw = $value->value();
        if (!is_bool($raw)) {
            throw new \LogicException('Expected boolean literal value');
        }

        return $raw;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function literalArrayValue(LiteralValue $value): array
    {
        $raw = $value->value();
        if (!is_array($raw)) {
            throw new \LogicException('Expected array literal value');
        }

        return $raw;
    }

    private function buildVariableExpression(Ast\Expression\VariableExpressionNode $expression): Expr
    {
        $name = $expression->name();
        if ($name instanceof Ast\Value\Identifier) {
            return new Expr\Variable($name->value());
        }

        return new Expr\Variable($this->buildExpression($name));
    }

    private function buildClassConstFetch(Ast\Expression\ClassConstantFetchExpressionNode $expression): Expr
    {
        $class = $expression->classReference();
        $classExpr = $class instanceof ExpressionNode
            ? $this->buildExpression($class)
            : $this->valueMapper->buildQualifiedName($class);

        return new Expr\ClassConstFetch(
            $classExpr,
            $this->valueMapper->buildIdentifier($expression->constant()),
        );
    }

    private function buildPropertyFetch(Ast\Expression\PropertyFetchExpressionNode $expression): Expr
    {
        $name = $expression->property();
        $property = $name instanceof Ast\Value\Identifier
            ? $this->valueMapper->buildIdentifier($name)
            : $this->buildExpression($name);

        if ($expression->isNullsafe()) {
            return new Expr\NullsafePropertyFetch(
                $this->buildExpression($expression->object()),
                $property,
            );
        }

        return new Expr\PropertyFetch(
            $this->buildExpression($expression->object()),
            $property,
        );
    }

    private function buildStaticPropertyFetch(Ast\Expression\StaticPropertyFetchExpressionNode $expression): Expr
    {
        $class = $expression->classReference();
        $classExpr = $class instanceof ExpressionNode
            ? $this->buildExpression($class)
            : $this->valueMapper->buildQualifiedName($class);

        $name = $expression->property();
        $property = $name instanceof Ast\Value\Identifier
            ? new Node\VarLikeIdentifier($name->value())
            : $this->buildExpression($name);

        return new Expr\StaticPropertyFetch($classExpr, $property);
    }

    private function buildArrayDimFetch(Ast\Expression\ArrayAccessExpressionNode $expression): Expr
    {
        return new Expr\ArrayDimFetch(
            $this->buildExpression($expression->array()),
            $expression->offset() !== null ? $this->buildExpression($expression->offset()) : null,
        );
    }

    private function buildArrayExpression(Ast\Expression\ArrayExpressionNode $expression): Expr
    {
        $node = new Expr\Array_($this->buildArrayItems($expression->items()));
        $node->setAttribute('kind', $expression->usesShortSyntax() ? Expr\Array_::KIND_SHORT : Expr\Array_::KIND_LONG);

        return $node;
    }

    private function buildFunctionCall(Ast\Expression\FunctionCallExpressionNode $expression): Expr
    {
        $callable = $expression->callable();
        $name = $callable instanceof ExpressionNode
            ? $this->buildExpression($callable)
            : $this->valueMapper->buildQualifiedName($callable);

        return new Expr\FuncCall(
            $name,
            $this->buildArguments($expression->arguments()),
        );
    }

    private function buildMethodCall(Ast\Expression\MethodCallExpressionNode $expression): Expr
    {
        $method = $expression->method();
        $name = $method instanceof Ast\Value\Identifier
            ? $this->valueMapper->buildIdentifier($method)
            : $this->buildExpression($method);

        if ($expression->isNullsafe()) {
            return new Expr\NullsafeMethodCall(
                $this->buildExpression($expression->object()),
                $name,
                $this->buildArguments($expression->arguments()),
            );
        }

        return new Expr\MethodCall(
            $this->buildExpression($expression->object()),
            $name,
            $this->buildArguments($expression->arguments()),
        );
    }

    private function buildStaticCall(Ast\Expression\StaticCallExpressionNode $expression): Expr
    {
        $class = $expression->classReference();
        $classExpr = $class instanceof ExpressionNode
            ? $this->buildExpression($class)
            : $this->valueMapper->buildQualifiedName($class);

        $method = $expression->method();
        $name = $method instanceof Ast\Value\Identifier
            ? $this->valueMapper->buildIdentifier($method)
            : $this->buildExpression($method);

        return new Expr\StaticCall(
            $classExpr,
            $name,
            $this->buildArguments($expression->arguments()),
        );
    }

    private function buildNewExpression(Ast\Expression\NewExpressionNode $expression): Expr
    {
        $class = $expression->classReference();
        $classExpr = $class instanceof ExpressionNode
            ? $this->buildExpression($class)
            : $this->valueMapper->buildQualifiedName($class);

        return new Expr\New_(
            $classExpr,
            $this->buildArguments($expression->arguments()),
        );
    }

    private function buildAnonymousClassExpression(Ast\Expression\AnonymousClassExpressionNode $expression): Expr
    {
        $classStmt = new Stmt\Class_(null, [
            'flags' => $this->valueMapper->buildClassFlags($expression->modifiers()),
            'extends' => $expression->extends() !== null ? $this->valueMapper->buildQualifiedName($expression->extends()) : null,
            'implements' => array_map(
                fn (Ast\Value\QualifiedName $name): Name => $this->valueMapper->buildQualifiedName($name),
                $expression->interfaces(),
            ),
            'stmts' => $this->context->memberMapper()->buildMembers($expression->members()),
            'attrGroups' => $this->valueMapper->buildAttributeGroups($expression->attributes()),
        ]);

        return new Expr\New_(
            $classStmt,
            $this->buildArguments($expression->constructorArguments()),
        );
    }

    private function buildCompoundAssignment(Ast\Expression\CompoundAssignmentExpressionNode $expression): Expr
    {
        $target = $this->buildExpression($expression->target());
        $value = $this->buildExpression($expression->value());

        return match ($expression->operator()) {
            AssignmentOperator::PLUS => new Expr\AssignOp\Plus($target, $value),
            AssignmentOperator::MINUS => new Expr\AssignOp\Minus($target, $value),
            AssignmentOperator::MULTIPLY => new Expr\AssignOp\Mul($target, $value),
            AssignmentOperator::DIVIDE => new Expr\AssignOp\Div($target, $value),
            AssignmentOperator::MODULO => new Expr\AssignOp\Mod($target, $value),
            AssignmentOperator::POWER => new Expr\AssignOp\Pow($target, $value),
            AssignmentOperator::CONCAT => new Expr\AssignOp\Concat($target, $value),
            AssignmentOperator::BITWISE_AND => new Expr\AssignOp\BitwiseAnd($target, $value),
            AssignmentOperator::BITWISE_OR => new Expr\AssignOp\BitwiseOr($target, $value),
            AssignmentOperator::BITWISE_XOR => new Expr\AssignOp\BitwiseXor($target, $value),
            AssignmentOperator::SHIFT_LEFT => new Expr\AssignOp\ShiftLeft($target, $value),
            AssignmentOperator::SHIFT_RIGHT => new Expr\AssignOp\ShiftRight($target, $value),
            AssignmentOperator::COALESCE => new Expr\AssignOp\Coalesce($target, $value),
            default => new Expr\Assign($target, $value),
        };
    }

    private function buildBinaryExpression(Ast\Expression\BinaryExpressionNode $expression): Expr
    {
        $left = $this->buildExpression($expression->left());
        $right = $this->buildExpression($expression->right());

        return match ($expression->operator()) {
            BinaryOperator::PLUS => new BinaryOp\Plus($left, $right),
            BinaryOperator::MINUS => new BinaryOp\Minus($left, $right),
            BinaryOperator::MULTIPLY => new BinaryOp\Mul($left, $right),
            BinaryOperator::DIVIDE => new BinaryOp\Div($left, $right),
            BinaryOperator::MODULO => new BinaryOp\Mod($left, $right),
            BinaryOperator::POWER => new BinaryOp\Pow($left, $right),
            BinaryOperator::CONCAT => new BinaryOp\Concat($left, $right),
            BinaryOperator::BITWISE_AND => new BinaryOp\BitwiseAnd($left, $right),
            BinaryOperator::BITWISE_OR => new BinaryOp\BitwiseOr($left, $right),
            BinaryOperator::BITWISE_XOR => new BinaryOp\BitwiseXor($left, $right),
            BinaryOperator::SHIFT_LEFT => new BinaryOp\ShiftLeft($left, $right),
            BinaryOperator::SHIFT_RIGHT => new BinaryOp\ShiftRight($left, $right),
            BinaryOperator::LOGICAL_AND => new BinaryOp\BooleanAnd($left, $right),
            BinaryOperator::LOGICAL_OR => new BinaryOp\BooleanOr($left, $right),
            BinaryOperator::LOGICAL_XOR => new BinaryOp\LogicalXor($left, $right),
            BinaryOperator::EQUAL => new BinaryOp\Equal($left, $right),
            BinaryOperator::IDENTICAL => new BinaryOp\Identical($left, $right),
            BinaryOperator::NOT_EQUAL => new BinaryOp\NotEqual($left, $right),
            BinaryOperator::NOT_IDENTICAL => new BinaryOp\NotIdentical($left, $right),
            BinaryOperator::GREATER => new BinaryOp\Greater($left, $right),
            BinaryOperator::GREATER_EQUAL => new BinaryOp\GreaterOrEqual($left, $right),
            BinaryOperator::SMALLER => new BinaryOp\Smaller($left, $right),
            BinaryOperator::SMALLER_EQUAL => new BinaryOp\SmallerOrEqual($left, $right),
            BinaryOperator::SPACESHIP => new BinaryOp\Spaceship($left, $right),
            BinaryOperator::COALESCE => new Expr\BinaryOp\Coalesce($left, $right),
            BinaryOperator::AND => new Expr\BinaryOp\LogicalAnd($left, $right),
            BinaryOperator::OR => new Expr\BinaryOp\LogicalOr($left, $right),
        };
    }

    private function buildUnaryExpression(Ast\Expression\UnaryExpressionNode $expression): Expr
    {
        $operand = $this->buildExpression($expression->operand());

        return match ($expression->operator()) {
            UnaryOperator::PLUS => new Expr\UnaryPlus($operand),
            UnaryOperator::MINUS => new Expr\UnaryMinus($operand),
            UnaryOperator::BITWISE_NOT => new Expr\BitwiseNot($operand),
            UnaryOperator::LOGICAL_NOT => new Expr\BooleanNot($operand),
            UnaryOperator::PRE_INCREMENT => new Expr\PreInc($operand),
            UnaryOperator::PRE_DECREMENT => new Expr\PreDec($operand),
            UnaryOperator::POST_INCREMENT => new Expr\PostInc($operand),
            UnaryOperator::POST_DECREMENT => new Expr\PostDec($operand),
        };
    }

    private function mapIncludeKind(IncludeKind $kind): int
    {
        return match ($kind) {
            IncludeKind::INCLUDE => Expr\Include_::TYPE_INCLUDE,
            IncludeKind::INCLUDE_ONCE => Expr\Include_::TYPE_INCLUDE_ONCE,
            IncludeKind::REQUIRE => Expr\Include_::TYPE_REQUIRE,
            IncludeKind::REQUIRE_ONCE => Expr\Include_::TYPE_REQUIRE_ONCE,
        };
    }

    /**
     * @param  list<string|ExpressionNode>            $parts
     * @return list<Expr|Node\InterpolatedStringPart>
     */
    private function buildShellParts(array $parts): array
    {
        return array_map(
            fn (string|ExpressionNode $part): Expr|Node\InterpolatedStringPart => is_string($part)
                ? new Node\InterpolatedStringPart($part)
                : $this->buildExpression($part),
            $parts,
        );
    }

    private function buildInstanceof(Ast\Expression\InstanceofExpressionNode $expression): Expr
    {
        $reference = $expression->classReference();
        if ($reference instanceof ExpressionNode) {
            $class = $this->buildExpression($reference);
        } elseif ($reference instanceof Ast\TypeNode) {
            $typeNode = $this->typeMapper->buildType($reference);
            if (!$typeNode instanceof Name) {
                throw new \RuntimeException('Instanceof expression requires a named type reference');
            }

            $class = $typeNode;
        } else {
            $class = $this->valueMapper->buildQualifiedName($reference);
        }

        return new Expr\Instanceof_(
            $this->buildExpression($expression->expression()),
            $class,
        );
    }

    private function buildCastExpression(Ast\Expression\CastExpressionNode $expression): Expr
    {
        $expr = $this->buildExpression($expression->expression());

        return match ($expression->type()) {
            Ast\Value\CastType::ARRAY => new Expr\Cast\Array_($expr),
            Ast\Value\CastType::BOOL => new Expr\Cast\Bool_($expr),
            Ast\Value\CastType::INT => new Expr\Cast\Int_($expr),
            Ast\Value\CastType::FLOAT => new Expr\Cast\Double($expr),
            Ast\Value\CastType::STRING => new Expr\Cast\String_($expr),
            Ast\Value\CastType::OBJECT => new Expr\Cast\Object_($expr),
            Ast\Value\CastType::UNSET => new Expr\Cast\Unset_($expr),
        };
    }

    private function buildExitExpression(Ast\Expression\ExitExpressionNode $expression): Expr
    {
        $argument = $expression->expression() !== null ? $this->buildExpression($expression->expression()) : null;
        $node = new Expr\Exit_($argument);
        $node->setAttribute(
            'kind',
            $expression->usesDieAlias() ? Expr\Exit_::KIND_DIE : Expr\Exit_::KIND_EXIT,
        );

        return $node;
    }

    private function buildEncapsedString(Ast\Expression\EncapsedStringExpressionNode $expression): Scalar\Encapsed
    {
        $parts = [];

        foreach ($expression->parts() as $part) {
            $parts[] = $part->part() instanceof ExpressionNode
                ? $this->buildExpression($part->part())
                : new Node\InterpolatedStringPart($part->part());
        }

        $encapsed = new Scalar\Encapsed($parts);
        $encapsed->setAttribute('kind', match ($expression->stringKind()) {
            EncapsedStringKind::HEREDOC => Scalar\String_::KIND_HEREDOC,
            EncapsedStringKind::NOWDOC => Scalar\String_::KIND_NOWDOC,
            default => Scalar\String_::KIND_DOUBLE_QUOTED,
        });

        return $encapsed;
    }

    private function buildClosureExpression(Ast\Expression\ClosureExpressionNode $expression): Expr
    {
        return new Expr\Closure([
            'static' => $expression->isStatic(),
            'byRef' => $expression->returnsByReference(),
            'params' => $this->buildParameters($expression->parameters()),
            'uses' => $this->buildClosureUses($expression->uses()),
            'returnType' => $this->typeMapper->buildType($expression->returnType()),
            'stmts' => $this->context->statementMapper()->buildStatements($expression->bodyStatements()),
            'attrGroups' => $this->valueMapper->buildAttributeGroups($expression->attributes()),
        ]);
    }

    private function buildArrowFunction(Ast\Expression\ArrowFunctionExpressionNode $expression): Expr
    {
        return new Expr\ArrowFunction([
            'static' => $expression->isStatic(),
            'byRef' => $expression->returnsByReference(),
            'params' => $this->buildParameters($expression->parameters()),
            'returnType' => $this->typeMapper->buildType($expression->returnType()),
            'expr' => $this->buildExpression($expression->body()),
            'attrGroups' => $this->valueMapper->buildAttributeGroups($expression->attributes()),
        ]);
    }

    private function buildMatchExpression(Ast\Expression\MatchExpressionNode $expression): Expr
    {
        return new Expr\Match_(
            $this->buildExpression($expression->expression()),
            $this->buildMatchArms($expression->arms()),
        );
    }

    private function buildYieldExpression(Ast\Expression\YieldExpressionNode $expression): Expr
    {
        if ($expression->value() === null) {
            return new Expr\Yield_();
        }

        if ($expression->key() === null) {
            return new Expr\Yield_($this->buildExpression($expression->value()));
        }

        return new Expr\Yield_(
            $this->buildExpression($expression->value()),
            $this->buildExpression($expression->key()),
        );
    }

    private function buildListExpression(Ast\Expression\ListExpressionNode $expression): Expr
    {
        $list = new Expr\List_($this->buildListItems($expression->items()));
        $value = $expression->value();

        if ($value instanceof Ast\Expression\LiteralExpressionNode && $value->value()->kind() === LiteralKind::NULL) {
            return $list;
        }

        return new Expr\Assign(
            $list,
            $this->buildExpression($value),
        );
    }
}
