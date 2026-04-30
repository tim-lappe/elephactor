<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_ as ScalarString;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;

final class ExpressionMapper
{
    use NodeAttributeMapperTrait;

    public function __construct(
        private readonly ValueMapper $valueMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    private function memberMapper(): MemberMapper
    {
        return $this->context->memberMapper();
    }

    private function statementMapper(): StatementMapper
    {
        return $this->context->statementMapper();
    }

    public function mapExpression(Expr $expression): Ast\ExpressionNode
    {
        $mapped = null;

        if ($expression instanceof Expr\Variable) {
            $mapped = $this->mapVariableExpression($expression);
        } elseif ($expression instanceof Expr\Assign) {
            $mapped = $this->mapAssignmentExpression($expression);
        } elseif ($expression instanceof Expr\AssignRef) {
            $mapped = new Ast\Expression\AssignmentExpressionNode(
                $this->mapExpression($expression->var),
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\AssignOp) {
            $mapped = $this->mapCompoundAssignmentExpression($expression);
        } elseif ($expression instanceof Expr\Array_) {
            $mapped = $this->mapArrayExpression($expression);
        } elseif ($expression instanceof Expr\ArrayDimFetch) {
            $mapped = $this->mapArrayAccessExpression($expression);
        } elseif ($expression instanceof Expr\List_) {
            $mapped = $this->mapListExpression($expression);
        } elseif ($expression instanceof Expr\FuncCall) {
            $mapped = $this->mapFunctionCall($expression);
        } elseif ($expression instanceof Expr\MethodCall) {
            $mapped = $this->mapMethodCall($expression, false);
        } elseif ($expression instanceof Expr\NullsafeMethodCall) {
            $mapped = $this->mapMethodCall($expression, true);
        } elseif ($expression instanceof Expr\StaticCall) {
            $mapped = $this->mapStaticCall($expression);
        } elseif ($expression instanceof Expr\PropertyFetch) {
            $mapped = $this->mapPropertyFetch($expression, false);
        } elseif ($expression instanceof Expr\NullsafePropertyFetch) {
            $mapped = $this->mapPropertyFetch($expression, true);
        } elseif ($expression instanceof Expr\StaticPropertyFetch) {
            $mapped = $this->mapStaticPropertyFetch($expression);
        } elseif ($expression instanceof Expr\ClassConstFetch) {
            $mapped = $this->mapClassConstantFetch($expression);
        } elseif ($expression instanceof Expr\ConstFetch) {
            $mapped = $this->mapConstFetchExpression($expression);
        } elseif ($expression instanceof Expr\BinaryOp) {
            $mapped = $this->mapBinaryExpression($expression);
        } elseif ($expression instanceof Expr\BooleanNot) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::LOGICAL_NOT);
        } elseif ($expression instanceof Expr\BitwiseNot) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::BITWISE_NOT);
        } elseif ($expression instanceof Expr\UnaryPlus) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::PLUS);
        } elseif ($expression instanceof Expr\UnaryMinus) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::MINUS);
        } elseif ($expression instanceof Expr\PostInc) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::POST_INCREMENT);
        } elseif ($expression instanceof Expr\PostDec) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::POST_DECREMENT);
        } elseif ($expression instanceof Expr\PreInc) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::PRE_INCREMENT);
        } elseif ($expression instanceof Expr\PreDec) {
            $mapped = $this->mapUnaryExpression($expression, Ast\Value\UnaryOperator::PRE_DECREMENT);
        } elseif ($expression instanceof Expr\Ternary) {
            $mapped = $this->mapTernaryExpression($expression);
        } elseif ($expression instanceof Expr\New_) {
            $mapped = $this->mapNewExpression($expression);
        } elseif ($expression instanceof Expr\Clone_) {
            $mapped = $this->mapCloneExpression($expression);
        } elseif ($expression instanceof Expr\Match_) {
            $mapped = $this->mapMatchExpression($expression);
        } elseif ($expression instanceof Expr\Yield_) {
            $mapped = $this->mapYieldExpression($expression);
        } elseif ($expression instanceof Expr\YieldFrom) {
            $mapped = new Ast\Expression\YieldFromExpressionNode(
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\Include_) {
            $mapped = $this->mapIncludeExpression($expression);
        } elseif ($expression instanceof Expr\Isset_) {
            $mapped = $this->mapIssetExpression($expression);
        } elseif ($expression instanceof Expr\Empty_) {
            $mapped = new Ast\Expression\EmptyExpressionNode(
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\Eval_) {
            $mapped = new Ast\Expression\EvalExpressionNode(
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\Exit_) {
            $mapped = new Ast\Expression\ExitExpressionNode(
                $expression->expr !== null ? $this->mapExpression($expression->expr) : null,
                false,
            );
        } elseif ($expression instanceof Expr\Print_) {
            $mapped = new Ast\Expression\PrintExpressionNode(
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\ShellExec) {
            $mapped = $this->mapShellCommandExpression($expression);
        } elseif ($expression instanceof Expr\Closure) {
            $mapped = $this->mapClosureExpression($expression);
        } elseif ($expression instanceof Expr\ArrowFunction) {
            $mapped = $this->mapArrowFunctionExpression($expression);
        } elseif ($expression instanceof Expr\ErrorSuppress) {
            $mapped = new Ast\Expression\ErrorSuppressExpressionNode(
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\Throw_) {
            $mapped = new Ast\Expression\ThrowExpressionNode(
                $this->mapExpression($expression->expr),
            );
        } elseif ($expression instanceof Expr\Instanceof_) {
            $mapped = $this->mapInstanceofExpression($expression);
        } elseif ($expression instanceof Expr\Cast) {
            $mapped = $this->mapCastExpression($expression);
        } elseif ($expression instanceof Node\Scalar) {
            $mapped = $this->mapLiteralExpression($expression);
        }

        if ($mapped !== null) {
            return $this->applyAttributes($expression, $mapped);
        }

        throw new \RuntimeException('Unsupported expression: ' . $expression::class);
    }

    private function mapVariableExpression(Expr\Variable $variable): Ast\Expression\VariableExpressionNode
    {
        $name = $variable->name;

        if (is_string($name)) {
            return $this->applyAttributes(
                $variable,
                new Ast\Expression\VariableExpressionNode(
                    $this->valueMapper->getTypeMapper()->mapIdentifier($name),
                ),
            );
        }

        /** @var Expr $nameExpr */
        $nameExpr = $name;

        return $this->applyAttributes(
            $variable,
            new Ast\Expression\VariableExpressionNode(
                $this->mapExpression($nameExpr),
            ),
        );
    }

    private function mapAssignmentExpression(Expr\Assign $assign): Ast\ExpressionNode
    {
        if ($assign->var instanceof Expr\List_) {
            return $this->mapListExpressionWithValue($assign->var, $assign->expr, $assign);
        }

        return $this->applyAttributes(
            $assign,
            new Ast\Expression\AssignmentExpressionNode(
                $this->mapExpression($assign->var),
                $this->mapExpression($assign->expr),
            ),
        );
    }

    private function mapListExpression(Expr\List_ $list): Ast\Expression\ListExpressionNode
    {
        return $this->applyAttributes(
            $list,
            new Ast\Expression\ListExpressionNode(
                $this->mapListItems($list->items),
                new Ast\Expression\LiteralExpressionNode(Ast\Value\LiteralValue::null()),
            ),
        );
    }

    private function mapListExpressionWithValue(Expr\List_ $list, Expr $value, Node $locationSource): Ast\Expression\ListExpressionNode
    {
        return $this->applyAttributes(
            $locationSource,
            new Ast\Expression\ListExpressionNode(
                $this->mapListItems($list->items),
                $this->mapExpression($value),
            ),
        );
    }

    /**
     * @param  array<\PhpParser\Node\Expr\ArrayItem|null> $items
     * @return list<Ast\Expression\ListItemNode>
     */
    private function mapListItems(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            if ($item === null) {
                continue;
            }

            $result[] = $this->applyAttributes(
                $item,
                new Ast\Expression\ListItemNode(
                    $item->key !== null ? $this->mapExpression($item->key) : null,
                    $this->mapExpression($item->value),
                ),
            );
        }

        return $result;
    }

    private function mapArrayExpression(Expr\Array_ $array): Ast\Expression\ArrayExpressionNode
    {
        $items = array_values(array_filter(
            $array->items,
            static fn (?Node\ArrayItem $item): bool => $item !== null,
        ));

        return $this->applyAttributes(
            $array,
            new Ast\Expression\ArrayExpressionNode(
                array_map(
                    fn (Node\ArrayItem $item): Ast\Expression\ArrayItemNode => $this->mapArrayItem($item),
                    $items,
                ),
                ($array->getAttribute('kind') ?? Expr\Array_::KIND_SHORT) === Expr\Array_::KIND_SHORT,
            ),
        );
    }

    private function mapArrayItem(Node\ArrayItem $item): Ast\Expression\ArrayItemNode
    {
        return $this->applyAttributes(
            $item,
            new Ast\Expression\ArrayItemNode(
                $this->mapExpression($item->value),
                $item->key !== null ? $this->mapExpression($item->key) : null,
                $item->byRef,
                $item->unpack,
            ),
        );
    }

    private function mapArrayAccessExpression(Expr\ArrayDimFetch $fetch): Ast\Expression\ArrayAccessExpressionNode
    {
        return $this->applyAttributes(
            $fetch,
            new Ast\Expression\ArrayAccessExpressionNode(
                $this->mapExpression($fetch->var),
                $fetch->dim !== null ? $this->mapExpression($fetch->dim) : null,
            ),
        );
    }

    private function mapFunctionCall(Expr\FuncCall $call): Ast\Expression\FunctionCallExpressionNode
    {
        $callable = $call->name instanceof Name
            ? $this->valueMapper->getTypeMapper()->mapQualifiedName($call->name)
            : $this->mapExpression($call->name);

        return $this->applyAttributes(
            $call,
            new Ast\Expression\FunctionCallExpressionNode(
                $callable,
                $this->mapArguments($call->args),
            ),
        );
    }

    private function mapMethodCall(Expr\MethodCall|Expr\NullsafeMethodCall $call, bool $nullsafe): Ast\Expression\MethodCallExpressionNode
    {
        $method = $call->name instanceof Expr
            ? $this->mapExpression($call->name)
            : $this->valueMapper->getTypeMapper()->mapIdentifier($call->name);

        return $this->applyAttributes(
            $call,
            new Ast\Expression\MethodCallExpressionNode(
                $this->mapExpression($call->var),
                $method,
                $this->mapArguments($call->args),
                $nullsafe,
            ),
        );
    }

    private function mapStaticCall(Expr\StaticCall $call): Ast\Expression\StaticCallExpressionNode
    {
        $classReference = $call->class instanceof Name
            ? $this->valueMapper->getTypeMapper()->mapQualifiedName($call->class)
            : $this->mapExpression($call->class);

        $method = $call->name instanceof Expr
            ? $this->mapExpression($call->name)
            : $this->valueMapper->getTypeMapper()->mapIdentifier($call->name);

        return $this->applyAttributes(
            $call,
            new Ast\Expression\StaticCallExpressionNode(
                $classReference,
                $method,
                $this->mapArguments($call->args),
            ),
        );
    }

    private function mapPropertyFetch(Expr\PropertyFetch|Expr\NullsafePropertyFetch $fetch, bool $nullsafe): Ast\Expression\PropertyFetchExpressionNode
    {
        if ($fetch->name instanceof Expr) {
            $property = $this->mapExpression($fetch->name);
        } elseif ($fetch->name instanceof Node\Identifier) {
            $property = $this->valueMapper->getTypeMapper()->mapIdentifier($fetch->name);
        } else {
            throw new \RuntimeException('Unsupported property fetch name');
        }

        return $this->applyAttributes(
            $fetch,
            new Ast\Expression\PropertyFetchExpressionNode(
                $this->mapExpression($fetch->var),
                $property,
                $nullsafe,
            ),
        );
    }

    private function mapStaticPropertyFetch(Expr\StaticPropertyFetch $fetch): Ast\Expression\StaticPropertyFetchExpressionNode
    {
        if ($fetch->name instanceof Expr) {
            $property = $this->mapExpression($fetch->name);
        } elseif ($fetch->name instanceof Node\Identifier) {
            $property = $this->valueMapper->getTypeMapper()->mapIdentifier($fetch->name);
        } else {
            throw new \RuntimeException('Unsupported static property fetch name');
        }

        $classReference = $fetch->class instanceof Name
            ? $this->valueMapper->getTypeMapper()->mapQualifiedName($fetch->class)
            : $this->mapExpression($fetch->class);

        return $this->applyAttributes(
            $fetch,
            new Ast\Expression\StaticPropertyFetchExpressionNode(
                $classReference,
                $property,
            ),
        );
    }

    private function mapClassConstantFetch(Expr\ClassConstFetch $fetch): Ast\Expression\ClassConstantFetchExpressionNode
    {
        $classReference = $fetch->class instanceof Name
            ? $this->valueMapper->getTypeMapper()->mapQualifiedName($fetch->class)
            : $this->mapExpression($fetch->class);

        if (!$fetch->name instanceof Node\Identifier) {
            throw new \RuntimeException('Unsupported class constant fetch name');
        }

        $constantName = $this->valueMapper->getTypeMapper()->mapIdentifier($fetch->name);

        return $this->applyAttributes(
            $fetch,
            new Ast\Expression\ClassConstantFetchExpressionNode(
                $classReference,
                $constantName,
            ),
        );
    }

    private function mapConstFetchExpression(Expr\ConstFetch $fetch): Ast\ExpressionNode
    {
        $lowerName = $fetch->name->toLowerString();

        $mapped = match ($lowerName) {
            'true' => new Ast\Expression\LiteralExpressionNode(
                Ast\Value\LiteralValue::boolean(true),
            ),
            'false' => new Ast\Expression\LiteralExpressionNode(
                Ast\Value\LiteralValue::boolean(false),
            ),
            'null' => new Ast\Expression\LiteralExpressionNode(
                Ast\Value\LiteralValue::null(),
            ),
            default => new Ast\Expression\ConstantFetchExpressionNode(
                $this->valueMapper->getTypeMapper()->mapQualifiedName($fetch->name),
            ),
        };

        return $this->applyAttributes($fetch, $mapped);
    }

    private function mapBinaryExpression(Expr\BinaryOp $binary): Ast\Expression\BinaryExpressionNode
    {
        return $this->applyAttributes(
            $binary,
            new Ast\Expression\BinaryExpressionNode(
                $this->mapBinaryOperator($binary),
                $this->mapExpression($binary->left),
                $this->mapExpression($binary->right),
            ),
        );
    }

    private function mapBinaryOperator(Expr\BinaryOp $binary): Ast\Value\BinaryOperator
    {
        return match ($binary::class) {
            Expr\BinaryOp\Plus::class => Ast\Value\BinaryOperator::PLUS,
            Expr\BinaryOp\Minus::class => Ast\Value\BinaryOperator::MINUS,
            Expr\BinaryOp\Mul::class => Ast\Value\BinaryOperator::MULTIPLY,
            Expr\BinaryOp\Div::class => Ast\Value\BinaryOperator::DIVIDE,
            Expr\BinaryOp\Mod::class => Ast\Value\BinaryOperator::MODULO,
            Expr\BinaryOp\Pow::class => Ast\Value\BinaryOperator::POWER,
            Expr\BinaryOp\Concat::class => Ast\Value\BinaryOperator::CONCAT,
            Expr\BinaryOp\BooleanAnd::class => Ast\Value\BinaryOperator::LOGICAL_AND,
            Expr\BinaryOp\BooleanOr::class => Ast\Value\BinaryOperator::LOGICAL_OR,
            Expr\BinaryOp\LogicalAnd::class => Ast\Value\BinaryOperator::AND,
            Expr\BinaryOp\LogicalOr::class => Ast\Value\BinaryOperator::OR,
            Expr\BinaryOp\LogicalXor::class => Ast\Value\BinaryOperator::LOGICAL_XOR,
            Expr\BinaryOp\BitwiseAnd::class => Ast\Value\BinaryOperator::BITWISE_AND,
            Expr\BinaryOp\BitwiseOr::class => Ast\Value\BinaryOperator::BITWISE_OR,
            Expr\BinaryOp\BitwiseXor::class => Ast\Value\BinaryOperator::BITWISE_XOR,
            Expr\BinaryOp\ShiftLeft::class => Ast\Value\BinaryOperator::SHIFT_LEFT,
            Expr\BinaryOp\ShiftRight::class => Ast\Value\BinaryOperator::SHIFT_RIGHT,
            Expr\BinaryOp\Equal::class => Ast\Value\BinaryOperator::EQUAL,
            Expr\BinaryOp\NotEqual::class => Ast\Value\BinaryOperator::NOT_EQUAL,
            Expr\BinaryOp\Identical::class => Ast\Value\BinaryOperator::IDENTICAL,
            Expr\BinaryOp\NotIdentical::class => Ast\Value\BinaryOperator::NOT_IDENTICAL,
            Expr\BinaryOp\Greater::class => Ast\Value\BinaryOperator::GREATER,
            Expr\BinaryOp\GreaterOrEqual::class => Ast\Value\BinaryOperator::GREATER_EQUAL,
            Expr\BinaryOp\Smaller::class => Ast\Value\BinaryOperator::SMALLER,
            Expr\BinaryOp\SmallerOrEqual::class => Ast\Value\BinaryOperator::SMALLER_EQUAL,
            Expr\BinaryOp\Spaceship::class => Ast\Value\BinaryOperator::SPACESHIP,
            Expr\BinaryOp\Coalesce::class => Ast\Value\BinaryOperator::COALESCE,
            default => throw new \RuntimeException('Unsupported binary operator'),
        };
    }

    private function mapUnaryExpression(Expr $node, Ast\Value\UnaryOperator $operator): Ast\Expression\UnaryExpressionNode
    {
        if ($node instanceof Expr\PostInc || $node instanceof Expr\PostDec || $node instanceof Expr\PreInc || $node instanceof Expr\PreDec) {
            $expression = $this->mapExpression($node->var);
        } elseif ($node instanceof Expr\UnaryPlus
            || $node instanceof Expr\UnaryMinus
            || $node instanceof Expr\BooleanNot
            || $node instanceof Expr\BitwiseNot) {
            $expression = $this->mapExpression($node->expr);
        } else {
            throw new \RuntimeException('Unsupported unary expression');
        }

        return $this->applyAttributes(
            $node,
            new Ast\Expression\UnaryExpressionNode(
                $operator,
                $expression,
            ),
        );
    }

    private function mapCompoundAssignmentExpression(Expr\AssignOp $assign): Ast\Expression\CompoundAssignmentExpressionNode
    {
        return $this->applyAttributes(
            $assign,
            new Ast\Expression\CompoundAssignmentExpressionNode(
                $this->mapAssignmentOperator($assign),
                $this->mapExpression($assign->var),
                $this->mapExpression($assign->expr),
            ),
        );
    }

    private function mapAssignmentOperator(Expr\AssignOp $assign): Ast\Value\AssignmentOperator
    {
        return match ($assign::class) {
            Expr\AssignOp\Plus::class => Ast\Value\AssignmentOperator::PLUS,
            Expr\AssignOp\Minus::class => Ast\Value\AssignmentOperator::MINUS,
            Expr\AssignOp\Mul::class => Ast\Value\AssignmentOperator::MULTIPLY,
            Expr\AssignOp\Div::class => Ast\Value\AssignmentOperator::DIVIDE,
            Expr\AssignOp\Mod::class => Ast\Value\AssignmentOperator::MODULO,
            Expr\AssignOp\Pow::class => Ast\Value\AssignmentOperator::POWER,
            Expr\AssignOp\Concat::class => Ast\Value\AssignmentOperator::CONCAT,
            Expr\AssignOp\BitwiseAnd::class => Ast\Value\AssignmentOperator::BITWISE_AND,
            Expr\AssignOp\BitwiseOr::class => Ast\Value\AssignmentOperator::BITWISE_OR,
            Expr\AssignOp\BitwiseXor::class => Ast\Value\AssignmentOperator::BITWISE_XOR,
            Expr\AssignOp\ShiftLeft::class => Ast\Value\AssignmentOperator::SHIFT_LEFT,
            Expr\AssignOp\ShiftRight::class => Ast\Value\AssignmentOperator::SHIFT_RIGHT,
            Expr\AssignOp\Coalesce::class => Ast\Value\AssignmentOperator::COALESCE,
            default => throw new \RuntimeException('Unsupported assignment operator'),
        };
    }

    private function mapTernaryExpression(Expr\Ternary $ternary): Ast\Expression\TernaryExpressionNode
    {
        return $this->applyAttributes(
            $ternary,
            new Ast\Expression\TernaryExpressionNode(
                $this->mapExpression($ternary->cond),
                $ternary->if !== null ? $this->mapExpression($ternary->if) : null,
                $this->mapExpression($ternary->else),
            ),
        );
    }

    private function mapNewExpression(Expr\New_ $new): Ast\ExpressionNode
    {
        if ($new->class instanceof Stmt\Class_) {
            return $this->mapAnonymousClassExpression($new);
        }

        $classReference = $new->class instanceof Name
            ? $this->valueMapper->getTypeMapper()->mapQualifiedName($new->class)
            : $this->mapExpression($new->class);

        return $this->applyAttributes(
            $new,
            new Ast\Expression\NewExpressionNode(
                $classReference,
                $this->mapArguments($new->args),
            ),
        );
    }

    private function mapAnonymousClassExpression(Expr\New_ $new): Ast\Expression\AnonymousClassExpressionNode
    {
        /** @var Stmt\Class_ $class */
        $class = $new->class;

        return $this->applyAttributes(
            $new,
            new Ast\Expression\AnonymousClassExpressionNode(
                $this->mapArguments($new->args),
                $this->valueMapper->mapAttributeGroups($class->attrGroups),
                array_values(array_map(
                    fn (Name $interface): Ast\Value\QualifiedName => $this->valueMapper->getTypeMapper()->mapQualifiedName($interface),
                    $class->implements,
                )),
                $this->memberMapper()->mapClassMembers($class->stmts),
                $this->valueMapper->mapClassModifiers($class->flags),
                $class->extends !== null ? $this->valueMapper->getTypeMapper()->mapQualifiedName($class->extends) : null,
            ),
        );
    }

    private function mapCloneExpression(Expr\Clone_ $clone): Ast\Expression\CloneExpressionNode
    {
        return $this->applyAttributes(
            $clone,
            new Ast\Expression\CloneExpressionNode(
                $this->mapExpression($clone->expr),
            ),
        );
    }

    private function mapMatchExpression(Expr\Match_ $match): Ast\Expression\MatchExpressionNode
    {
        return $this->applyAttributes(
            $match,
            new Ast\Expression\MatchExpressionNode(
                $this->mapExpression($match->cond),
                array_values(array_map(
                    fn (Node\MatchArm $arm): Ast\Expression\MatchArmNode => $this->mapMatchArm($arm),
                    $match->arms,
                )),
            ),
        );
    }

    private function mapMatchArm(Node\MatchArm $arm): Ast\Expression\MatchArmNode
    {
        $conditions = $arm->conds !== null
            ? array_map(fn (Expr $expr): Ast\ExpressionNode => $this->mapExpression($expr), $arm->conds)
            : [];

        return $this->applyAttributes(
            $arm,
            new Ast\Expression\MatchArmNode(
                $conditions,
                $this->mapExpression($arm->body),
            ),
        );
    }

    private function mapYieldExpression(Expr\Yield_ $yield): Ast\Expression\YieldExpressionNode
    {
        return $this->applyAttributes(
            $yield,
            new Ast\Expression\YieldExpressionNode(
                $yield->value !== null ? $this->mapExpression($yield->value) : null,
                $yield->key !== null ? $this->mapExpression($yield->key) : null,
            ),
        );
    }

    private function mapIncludeExpression(Expr\Include_ $include): Ast\Expression\IncludeExpressionNode
    {
        $kind = match ($include->type) {
            Expr\Include_::TYPE_INCLUDE => Ast\Value\IncludeKind::INCLUDE,
            Expr\Include_::TYPE_INCLUDE_ONCE => Ast\Value\IncludeKind::INCLUDE_ONCE,
            Expr\Include_::TYPE_REQUIRE => Ast\Value\IncludeKind::REQUIRE,
            Expr\Include_::TYPE_REQUIRE_ONCE => Ast\Value\IncludeKind::REQUIRE_ONCE,
            default => throw new \RuntimeException('Unsupported include type'),
        };

        return $this->applyAttributes(
            $include,
            new Ast\Expression\IncludeExpressionNode(
                $kind,
                $this->mapExpression($include->expr),
            ),
        );
    }

    private function mapIssetExpression(Expr\Isset_ $isset): Ast\Expression\IssetExpressionNode
    {
        return $this->applyAttributes(
            $isset,
            new Ast\Expression\IssetExpressionNode(
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->mapExpression($expr), $isset->vars)),
            ),
        );
    }

    private function mapShellCommandExpression(Expr\ShellExec $shellExec): Ast\Expression\ShellCommandExpressionNode
    {
        $parts = [];

        foreach ($shellExec->parts as $part) {
            if ($part instanceof Node\InterpolatedStringPart) {
                $parts[] = $part->value;
                continue;
            }

            $parts[] = $this->mapExpression($part);
        }

        return $this->applyAttributes(
            $shellExec,
            new Ast\Expression\ShellCommandExpressionNode(
                $parts,
            ),
        );
    }

    private function mapClosureExpression(Expr\Closure $closure): Ast\Expression\ClosureExpressionNode
    {
        return $this->applyAttributes(
            $closure,
            new Ast\Expression\ClosureExpressionNode(
                $this->valueMapper->mapAttributeGroups($closure->attrGroups),
                $this->mapParameters($closure->params),
                array_values(array_map(
                    fn (Expr\ClosureUse $use): Ast\Expression\ClosureUseVariableNode => $this->mapClosureUse($use),
                    $closure->uses,
                )),
                $this->statementMapper()->mapStatements($closure->stmts ?? []),
                $this->valueMapper->getTypeMapper()->mapType($closure->returnType),
                $closure->static,
                $closure->byRef,
            ),
        );
    }

    private function mapArrowFunctionExpression(Expr\ArrowFunction $arrow): Ast\Expression\ArrowFunctionExpressionNode
    {
        return $this->applyAttributes(
            $arrow,
            new Ast\Expression\ArrowFunctionExpressionNode(
                $this->valueMapper->mapAttributeGroups($arrow->attrGroups),
                $this->mapParameters($arrow->params),
                $this->mapExpression($arrow->expr),
                $this->valueMapper->getTypeMapper()->mapType($arrow->returnType),
                $arrow->static,
                $arrow->byRef,
            ),
        );
    }

    private function mapClosureUse(Expr\ClosureUse $use): Ast\Expression\ClosureUseVariableNode
    {
        return $this->applyAttributes(
            $use,
            new Ast\Expression\ClosureUseVariableNode(
                $this->expectSimpleVariable($use->var),
                $use->byRef,
            ),
        );
    }

    private function mapInstanceofExpression(Expr\Instanceof_ $instanceof): Ast\Expression\InstanceofExpressionNode
    {
        if ($instanceof->class instanceof Name) {
            $reference = $this->valueMapper->getTypeMapper()->mapQualifiedName($instanceof->class);
        } elseif ($instanceof->class instanceof Expr) {
            $reference = $this->mapExpression($instanceof->class);
        } else {
            throw new \RuntimeException('Unsupported instanceof class');
        }

        return $this->applyAttributes(
            $instanceof,
            new Ast\Expression\InstanceofExpressionNode(
                $this->mapExpression($instanceof->expr),
                $reference,
            ),
        );
    }

    private function mapCastExpression(Expr\Cast $cast): Ast\Expression\CastExpressionNode
    {
        $type = match ($cast::class) {
            Expr\Cast\Array_::class => Ast\Value\CastType::ARRAY,
            Expr\Cast\Bool_::class => Ast\Value\CastType::BOOL,
            Expr\Cast\Int_::class => Ast\Value\CastType::INT,
            Expr\Cast\Double::class => Ast\Value\CastType::FLOAT,
            Expr\Cast\String_::class => Ast\Value\CastType::STRING,
            Expr\Cast\Object_::class => Ast\Value\CastType::OBJECT,
            Expr\Cast\Unset_::class => Ast\Value\CastType::UNSET,
            default => throw new \RuntimeException('Unsupported cast type'),
        };

        return $this->applyAttributes(
            $cast,
            new Ast\Expression\CastExpressionNode(
                $type,
                $this->mapExpression($cast->expr),
            ),
        );
    }

    private function mapLiteralExpression(Node\Scalar $scalar): Ast\ExpressionNode
    {
        $mapped = null;

        if ($scalar instanceof Node\Scalar\LNumber) {
            $mapped = new Ast\Expression\LiteralExpressionNode(
                Ast\Value\LiteralValue::integer($scalar->value),
            );
        }

        if ($scalar instanceof Node\Scalar\DNumber) {
            $mapped = new Ast\Expression\LiteralExpressionNode(
                Ast\Value\LiteralValue::float($scalar->value),
            );
        }

        if ($scalar instanceof Node\Scalar\String_) {
            $mapped = new Ast\Expression\LiteralExpressionNode(
                Ast\Value\LiteralValue::string($scalar->value),
            );
        }

        if ($scalar instanceof Node\Scalar\Encapsed) {
            return $this->mapEncapsedStringExpression($scalar);
        }

        if ($scalar instanceof Node\Scalar\MagicConst) {
            $mapped = new Ast\Expression\ConstantFetchExpressionNode(
                new Ast\Value\QualifiedName([new Ast\Value\Identifier($scalar->getName())]),
            );
        }

        if ($mapped === null) {
            throw new \RuntimeException('Unsupported scalar type');
        }

        return $this->applyAttributes($scalar, $mapped);
    }

    private function mapEncapsedStringExpression(Node\Scalar\Encapsed $encapsed): Ast\Expression\EncapsedStringExpressionNode
    {
        $kindAttribute = $encapsed->getAttribute('kind');
        $kind = match ($kindAttribute) {
            ScalarString::KIND_HEREDOC => Ast\Value\EncapsedStringKind::HEREDOC,
            ScalarString::KIND_NOWDOC => Ast\Value\EncapsedStringKind::NOWDOC,
            default => Ast\Value\EncapsedStringKind::DOUBLE_QUOTED,
        };

        $parts = [];

        foreach ($encapsed->parts as $part) {
            if ($part instanceof Node\InterpolatedStringPart) {
                $parts[] = $this->mapEncapsedStringPart($part);
                continue;
            }

            $parts[] = new Ast\Expression\EncapsedStringPartNode(
                $this->mapExpression($part),
            );
        }

        return $this->applyAttributes(
            $encapsed,
            new Ast\Expression\EncapsedStringExpressionNode(
                $kind,
                $parts,
            ),
        );
    }

    private function mapEncapsedStringPart(Node\InterpolatedStringPart $part): Ast\Expression\EncapsedStringPartNode
    {
        return $this->applyAttributes(
            $part,
            new Ast\Expression\EncapsedStringPartNode(
                $part->value,
            ),
        );
    }

    /**
     * @param  array<Node\Param>                $parameters
     * @return list<Ast\Argument\ParameterNode>
     */
    public function mapParameters(array $parameters): array
    {
        return array_values(array_map(
            fn (Node\Param $param): Ast\Argument\ParameterNode => $this->mapParameter($param),
            $parameters,
        ));
    }

    private function mapParameter(Node\Param $parameter): Ast\Argument\ParameterNode
    {
        $visibility = null;
        if (($parameter->flags & Stmt\Class_::VISIBILITY_MODIFIER_MASK) !== 0) {
            $visibility = $this->valueMapper->mapVisibility($parameter->flags);
        }

        if ($parameter->var instanceof Expr\Error) {
            throw new \RuntimeException('Unsupported parameter variable');
        }

        return $this->applyAttributes(
            $parameter,
            new Ast\Argument\ParameterNode(
                $this->expectSimpleVariable($parameter->var),
                $this->valueMapper->getTypeMapper()->mapType($parameter->type),
                $parameter->byRef ? Ast\Value\ParameterPassingMode::BY_REFERENCE : Ast\Value\ParameterPassingMode::BY_VALUE,
                $parameter->variadic,
                $parameter->default !== null ? $this->mapExpression($parameter->default) : null,
                $visibility,
                ($parameter->flags & Stmt\Class_::MODIFIER_READONLY) === Stmt\Class_::MODIFIER_READONLY,
                $this->valueMapper->mapAttributeGroups($parameter->attrGroups),
            ),
        );
    }

    /**
     * @param  array<Node\Arg|Node\VariadicPlaceholder> $arguments
     * @return list<Ast\Argument\ArgumentNode>
     */
    public function mapArguments(array $arguments): array
    {
        $result = [];
        foreach ($arguments as $argument) {
            if ($argument instanceof Node\Arg) {
                $result[] = $this->mapArgument($argument);
            } elseif ($argument instanceof Node\VariadicPlaceholder) {
                // Handle variadic placeholder - this might need special handling
                // For now, skip it or handle as a special argument
                continue;
            }
        }
        return $result;
    }

    private function mapArgument(Node\Arg $argument): Ast\Argument\ArgumentNode
    {
        return $this->applyAttributes(
            $argument,
            new Ast\Argument\ArgumentNode(
                $this->mapExpression($argument->value),
                $argument->name !== null ? $this->valueMapper->getTypeMapper()->mapIdentifier($argument->name) : null,
                $argument->unpack,
            ),
        );
    }

    public function expectSimpleVariable(Expr\Variable $variable): Ast\Value\Identifier
    {
        if (!is_string($variable->name)) {
            throw new \RuntimeException('Complex variable name');
        }

        return new Ast\Value\Identifier($variable->name);
    }
}
