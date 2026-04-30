<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\TokenSpanExtraBlankLines;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;

final class StatementMapper
{
    use NodeAttributeMapperTrait;

    public function __construct(
        private readonly MemberMapper $memberMapper,
        private readonly ValueMapper $valueMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    private function expressionMapper(): ExpressionMapper
    {
        return $this->context->expressionMapper();
    }

    /**
     * @param  Node\Stmt[]             $statements
     * @return list<Ast\StatementNode>
     */
    public function mapStatements(array $statements): array
    {
        $result = [];

        $tokens = $this->context->sourceTokens();
        $prevStmt = null;

        foreach ($statements as $statement) {
            if ($statement instanceof Stmt\Nop) {
                continue;
            }

            foreach ($this->mapStatement($statement) as $segmentIndex => $mapped) {
                if ($prevStmt !== null && $segmentIndex === 0 && $mapped instanceof Ast\AbstractNode) {
                    $extra = TokenSpanExtraBlankLines::betweenNodes($prevStmt, $statement, $tokens);
                    if ($extra > 0) {
                        $mapped->setLeadingExtraNewlinesBefore($extra);
                    }
                }
                $result[] = $mapped;
            }

            $prevStmt = $statement;
        }

        return $result;
    }

    /**
     * @return list<Ast\StatementNode>
     */
    public function mapStatement(Node\Stmt $statement): array
    {
        if ($statement instanceof Stmt\Namespace_) {
            return [$this->mapNamespace($statement)];
        }

        if ($statement instanceof Stmt\Use_) {
            return [$this->mapUseStatement($statement)];
        }

        if ($statement instanceof Stmt\GroupUse) {
            return $this->mapGroupUse($statement);
        }

        if ($statement instanceof Stmt\Class_) {
            return [$this->mapClassDeclaration($statement)];
        }

        if ($statement instanceof Stmt\Interface_) {
            return [$this->mapInterfaceDeclaration($statement)];
        }

        if ($statement instanceof Stmt\Trait_) {
            return [$this->mapTraitDeclaration($statement)];
        }

        if ($statement instanceof Stmt\Enum_) {
            return [$this->mapEnumDeclaration($statement)];
        }

        if ($statement instanceof Stmt\Function_) {
            return [$this->mapFunctionDeclaration($statement)];
        }

        if ($statement instanceof Stmt\Const_) {
            return [$this->mapConstDeclaration($statement)];
        }

        if ($statement instanceof Stmt\Return_) {
            return [$this->mapReturnStatement($statement)];
        }

        if ($statement instanceof Stmt\If_) {
            return [$this->mapIfStatement($statement)];
        }

        if ($statement instanceof Stmt\While_) {
            return [$this->mapWhileStatement($statement)];
        }

        if ($statement instanceof Stmt\Do_) {
            return [$this->mapDoWhileStatement($statement)];
        }

        if ($statement instanceof Stmt\For_) {
            return [$this->mapForStatement($statement)];
        }

        if ($statement instanceof Stmt\Foreach_) {
            return [$this->mapForeachStatement($statement)];
        }

        if ($statement instanceof Stmt\Switch_) {
            return [$this->mapSwitchStatement($statement)];
        }

        if ($statement instanceof Stmt\TryCatch) {
            return [$this->mapTryStatement($statement)];
        }

        // Note: Throw is actually an expression, not a statement in PHP-Parser
        // It's handled in mapExpressionStatement

        if ($statement instanceof Stmt\Break_) {
            return [$this->mapBreakStatement($statement)];
        }

        if ($statement instanceof Stmt\Continue_) {
            return [$this->mapContinueStatement($statement)];
        }

        if ($statement instanceof Stmt\Echo_) {
            return [$this->mapEchoStatement($statement)];
        }

        if ($statement instanceof Stmt\Global_) {
            return [$this->mapGlobalStatement($statement)];
        }

        if ($statement instanceof Stmt\Static_) {
            return [$this->mapStaticStatement($statement)];
        }

        if ($statement instanceof Stmt\Unset_) {
            return [$this->mapUnsetStatement($statement)];
        }

        if ($statement instanceof Stmt\Declare_) {
            return [$this->mapDeclareStatement($statement)];
        }

        if ($statement instanceof Stmt\Goto_) {
            return [$this->mapGotoStatement($statement)];
        }

        if ($statement instanceof Stmt\Label) {
            return [$this->mapLabelStatement($statement)];
        }

        if ($statement instanceof Stmt\InlineHTML) {
            return [$this->mapInlineHtmlStatement($statement)];
        }

        if ($statement instanceof Stmt\HaltCompiler) {
            return [$this->mapHaltCompilerStatement($statement)];
        }

        if ($statement instanceof Stmt\Block) {
            return [$this->mapBlockStatement($statement->stmts, $statement)];
        }

        if ($statement instanceof Stmt\Expression) {
            return [$this->mapExpressionStatement($statement)];
        }

        throw new \RuntimeException('Unsupported statement: ' . $statement::class);
    }

    private function mapNamespace(Stmt\Namespace_ $namespace): Ast\Statement\NamespaceDefinitionNode
    {
        $name = $namespace->name !== null ? $this->valueMapper->getTypeMapper()->mapQualifiedName($namespace->name) : null;
        $kind = $namespace->getAttribute('kind');
        $statements = $this->mapStatements($namespace->stmts);

        if ($name === null) {
            throw new \RuntimeException('Namespace name is required');
        }

        return $this->applyAttributes(
            $namespace,
            new Ast\Statement\NamespaceDefinitionNode(
                $name,
                $statements,
                $kind === Stmt\Namespace_::KIND_BRACED,
            ),
        );
    }

    private function mapUseStatement(Stmt\Use_ $use): Ast\Statement\UseStatementNode
    {
        return $this->applyAttributes(
            $use,
            new Ast\Statement\UseStatementNode(
                array_values(array_map(
                    fn (Node\UseItem $item): Ast\Statement\UseClauseNode => $this->mapUseClause($item),
                    $use->uses,
                )),
                $this->valueMapper->resolveUseKind($use->type),
                null,
            ),
        );
    }

    /**
     * @return list<Ast\Statement\UseStatementNode>
     */
    private function mapGroupUse(Stmt\GroupUse $groupUse): array
    {
        $groupedClauses = [];

        foreach ($groupUse->uses as $item) {
            $type = $item->type === Stmt\Use_::TYPE_UNKNOWN ? $groupUse->type : $item->type;
            $kind = $this->valueMapper->resolveUseKind($type);
            $groupedClauses[$kind->value][] = $this->mapUseClause($item);
        }

        $prefix = $this->valueMapper->getTypeMapper()->mapQualifiedName($groupUse->prefix);
        $result = [];

        foreach ($groupedClauses as $kindValue => $clauses) {
            $result[] = $this->applyAttributes(
                $groupUse,
                new Ast\Statement\UseStatementNode(
                    $clauses,
                    Ast\Statement\UseKind::from($kindValue),
                    $prefix,
                ),
            );
        }

        return $result;
    }

    private function mapUseClause(Node\UseItem $item): Ast\Statement\UseClauseNode
    {
        return $this->applyAttributes(
            $item,
            new Ast\Statement\UseClauseNode(
                $this->valueMapper->getTypeMapper()->mapQualifiedName($item->name),
                $item->alias !== null ? $this->valueMapper->getTypeMapper()->mapIdentifier($item->alias) : null,
            ),
        );
    }

    private function mapConstDeclaration(Stmt\Const_ $const): Ast\Declaration\ConstDeclarationNode
    {
        return $this->applyAttributes(
            $const,
            new Ast\Declaration\ConstDeclarationNode(
                array_values(array_map(
                    fn (Node\Const_ $element): Ast\Declaration\ConstElementNode => new Ast\Declaration\ConstElementNode(
                        $this->valueMapper->getTypeMapper()->mapIdentifier($element->name),
                        $this->expressionMapper()->mapExpression($element->value),
                    ),
                    $const->consts,
                )),
            ),
        );
    }

    private function mapFunctionDeclaration(Stmt\Function_ $function): Ast\Declaration\FunctionDeclarationNode
    {
        return $this->applyAttributes(
            $function,
            new Ast\Declaration\FunctionDeclarationNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($function->name),
                $this->valueMapper->mapAttributeGroups($function->attrGroups),
                $this->expressionMapper()->mapParameters($function->params),
                $this->mapStatements($function->stmts ?? []),
                $this->valueMapper->getTypeMapper()->mapType($function->returnType),
                $function->byRef,
                $this->valueMapper->mapDocBlock($function->getDocComment()),
            ),
        );
    }

    private function mapClassDeclaration(Stmt\Class_ $class): Ast\Declaration\ClassDeclarationNode
    {
        if ($class->name === null) {
            throw new \RuntimeException('Class name is required');
        }

        $extends = $class->extends !== null ? $this->valueMapper->getTypeMapper()->mapQualifiedName($class->extends) : null;
        $interfaces = array_values(array_map(
            fn (Name $interface): Ast\Value\QualifiedName => $this->valueMapper->getTypeMapper()->mapQualifiedName($interface),
            $class->implements,
        ));

        return $this->applyAttributes(
            $class,
            new Ast\Declaration\ClassDeclarationNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($class->name),
                $this->valueMapper->mapClassModifiers($class->flags),
                $this->valueMapper->mapAttributeGroups($class->attrGroups),
                $interfaces,
                $this->memberMapper->mapClassMembers($class->stmts),
                $extends,
                $this->valueMapper->mapDocBlock($class->getDocComment()),
            ),
        );
    }

    private function mapInterfaceDeclaration(Stmt\Interface_ $interface): Ast\Declaration\InterfaceDeclarationNode
    {
        if ($interface->name === null) {
            throw new \RuntimeException('Interface name is required');
        }

        return $this->applyAttributes(
            $interface,
            new Ast\Declaration\InterfaceDeclarationNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($interface->name),
                $this->valueMapper->mapAttributeGroups($interface->attrGroups),
                array_values(array_map(
                    fn (Name $parent): Ast\Value\QualifiedName => $this->valueMapper->getTypeMapper()->mapQualifiedName($parent),
                    $interface->extends,
                )),
                $this->memberMapper->mapClassMembers($interface->stmts),
                $this->valueMapper->mapDocBlock($interface->getDocComment()),
            ),
        );
    }

    private function mapTraitDeclaration(Stmt\Trait_ $trait): Ast\Declaration\TraitDeclarationNode
    {
        if ($trait->name === null) {
            throw new \RuntimeException('Trait name is required');
        }

        return $this->applyAttributes(
            $trait,
            new Ast\Declaration\TraitDeclarationNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($trait->name),
                $this->valueMapper->mapAttributeGroups($trait->attrGroups),
                $this->memberMapper->mapClassMembers($trait->stmts),
                $this->valueMapper->mapDocBlock($trait->getDocComment()),
            ),
        );
    }

    private function mapEnumDeclaration(Stmt\Enum_ $enum): Ast\Declaration\EnumDeclarationNode
    {
        if ($enum->name === null) {
            throw new \RuntimeException('Enum name is required');
        }

        return $this->applyAttributes(
            $enum,
            new Ast\Declaration\EnumDeclarationNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($enum->name),
                $this->valueMapper->mapAttributeGroups($enum->attrGroups),
                array_values(array_map(
                    fn (Name $interface): Ast\Value\QualifiedName => $this->valueMapper->getTypeMapper()->mapQualifiedName($interface),
                    $enum->implements,
                )),
                $this->memberMapper->mapClassMembers($enum->stmts),
                $this->valueMapper->getTypeMapper()->mapType($enum->scalarType),
                $this->valueMapper->mapDocBlock($enum->getDocComment()),
            ),
        );
    }

    private function mapReturnStatement(Stmt\Return_ $return): Ast\Statement\ReturnStatementNode
    {
        return $this->applyAttributes(
            $return,
            new Ast\Statement\ReturnStatementNode(
                $return->expr !== null ? $this->expressionMapper()->mapExpression($return->expr) : null,
            ),
        );
    }

    private function mapBreakStatement(Stmt\Break_ $break): Ast\Statement\BreakStatementNode
    {
        return $this->applyAttributes(
            $break,
            new Ast\Statement\BreakStatementNode(
                $break->num !== null ? $this->expressionMapper()->mapExpression($break->num) : null,
            ),
        );
    }

    private function mapContinueStatement(Stmt\Continue_ $continue): Ast\Statement\ContinueStatementNode
    {
        return $this->applyAttributes(
            $continue,
            new Ast\Statement\ContinueStatementNode(
                $continue->num !== null ? $this->expressionMapper()->mapExpression($continue->num) : null,
            ),
        );
    }


    private function mapExpressionStatement(Stmt\Expression $expression): Ast\StatementNode
    {
        if ($expression->expr instanceof Expr\Throw_) {
            return $this->applyAttributes(
                $expression,
                new Ast\Statement\ThrowStatementNode(
                    $this->expressionMapper()->mapExpression($expression->expr->expr),
                ),
            );
        }

        return $this->applyAttributes(
            $expression,
            new Ast\Statement\ExpressionStatementNode(
                $this->expressionMapper()->mapExpression($expression->expr),
            ),
        );
    }

    private function mapIfStatement(Stmt\If_ $if): Ast\Statement\IfStatementNode
    {
        return $this->applyAttributes(
            $if,
            new Ast\Statement\IfStatementNode(
                $this->expressionMapper()->mapExpression($if->cond),
                $this->mapStatements($if->stmts),
                array_values(array_map(
                    fn (Stmt\ElseIf_ $elseIf): Ast\Statement\ElseIfClauseNode => $this->mapElseIfClause($elseIf),
                    $if->elseifs,
                )),
                $if->else !== null ? $this->mapElseClause($if->else) : null,
            ),
        );
    }

    private function mapElseIfClause(Stmt\ElseIf_ $elseIf): Ast\Statement\ElseIfClauseNode
    {
        return $this->applyAttributes(
            $elseIf,
            new Ast\Statement\ElseIfClauseNode(
                $this->expressionMapper()->mapExpression($elseIf->cond),
                $this->mapStatements($elseIf->stmts),
            ),
        );
    }

    private function mapElseClause(Stmt\Else_ $else): Ast\Statement\ElseClauseNode
    {
        return $this->applyAttributes(
            $else,
            new Ast\Statement\ElseClauseNode(
                $this->mapStatements($else->stmts),
            ),
        );
    }

    private function mapWhileStatement(Stmt\While_ $while): Ast\Statement\WhileStatementNode
    {
        return $this->applyAttributes(
            $while,
            new Ast\Statement\WhileStatementNode(
                $this->expressionMapper()->mapExpression($while->cond),
                $this->mapStatements($while->stmts),
            ),
        );
    }

    private function mapDoWhileStatement(Stmt\Do_ $do): Ast\Statement\DoWhileStatementNode
    {
        return $this->applyAttributes(
            $do,
            new Ast\Statement\DoWhileStatementNode(
                $this->expressionMapper()->mapExpression($do->cond),
                $this->mapStatements($do->stmts),
            ),
        );
    }

    private function mapForStatement(Stmt\For_ $for): Ast\Statement\ForStatementNode
    {
        return $this->applyAttributes(
            $for,
            new Ast\Statement\ForStatementNode(
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->expressionMapper()->mapExpression($expr), $for->init)),
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->expressionMapper()->mapExpression($expr), $for->cond)),
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->expressionMapper()->mapExpression($expr), $for->loop)),
                $this->mapStatements($for->stmts),
            ),
        );
    }

    private function mapForeachStatement(Stmt\Foreach_ $foreach): Ast\Statement\ForeachStatementNode
    {
        return $this->applyAttributes(
            $foreach,
            new Ast\Statement\ForeachStatementNode(
                $this->expressionMapper()->mapExpression($foreach->expr),
                $this->expressionMapper()->mapExpression($foreach->valueVar),
                $foreach->keyVar !== null ? $this->expressionMapper()->mapExpression($foreach->keyVar) : null,
                $foreach->byRef,
                $this->mapStatements($foreach->stmts),
            ),
        );
    }

    private function mapSwitchStatement(Stmt\Switch_ $switch): Ast\Statement\SwitchStatementNode
    {
        return $this->applyAttributes(
            $switch,
            new Ast\Statement\SwitchStatementNode(
                $this->expressionMapper()->mapExpression($switch->cond),
                array_values(array_map(
                    fn (Stmt\Case_ $case): Ast\Statement\SwitchCaseNode => $this->mapSwitchCase($case),
                    $switch->cases,
                )),
            ),
        );
    }

    private function mapSwitchCase(Stmt\Case_ $case): Ast\Statement\SwitchCaseNode
    {
        return $this->applyAttributes(
            $case,
            new Ast\Statement\SwitchCaseNode(
                $case->cond !== null ? $this->expressionMapper()->mapExpression($case->cond) : null,
                $this->mapStatements($case->stmts),
            ),
        );
    }

    private function mapTryStatement(Stmt\TryCatch $try): Ast\Statement\TryStatementNode
    {
        return $this->applyAttributes(
            $try,
            new Ast\Statement\TryStatementNode(
                $this->mapStatements($try->stmts),
                array_values(array_map(
                    fn (Stmt\Catch_ $catch): Ast\Statement\CatchClauseNode => $this->mapCatchClause($catch),
                    $try->catches,
                )),
                $try->finally !== null ? $this->mapFinallyClause($try->finally) : null,
            ),
        );
    }

    private function mapCatchClause(Stmt\Catch_ $catch): Ast\Statement\CatchClauseNode
    {
        $types = array_values(array_filter(array_map(
            fn (Node $type): ?Ast\TypeNode => $this->valueMapper->getTypeMapper()->mapType($type),
            $catch->types,
        )));

        if ($catch->var === null) {
            throw new \RuntimeException('Catch variable is required');
        }

        return $this->applyAttributes(
            $catch,
            new Ast\Statement\CatchClauseNode(
                $types,
                $this->expressionMapper()->expectSimpleVariable($catch->var),
                $this->mapStatements($catch->stmts),
            ),
        );
    }

    private function mapFinallyClause(Stmt\Finally_ $finally): Ast\Statement\FinallyClauseNode
    {
        return $this->applyAttributes(
            $finally,
            new Ast\Statement\FinallyClauseNode(
                $this->mapStatements($finally->stmts),
            ),
        );
    }

    private function mapEchoStatement(Stmt\Echo_ $echo): Ast\Statement\EchoStatementNode
    {
        return $this->applyAttributes(
            $echo,
            new Ast\Statement\EchoStatementNode(
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->expressionMapper()->mapExpression($expr), $echo->exprs)),
            ),
        );
    }

    private function mapGlobalStatement(Stmt\Global_ $global): Ast\Statement\GlobalStatementNode
    {
        return $this->applyAttributes(
            $global,
            new Ast\Statement\GlobalStatementNode(
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->expressionMapper()->mapExpression($expr), $global->vars)),
            ),
        );
    }

    private function mapStaticStatement(Stmt\Static_ $static): Ast\Statement\StaticStatementNode
    {
        return $this->applyAttributes(
            $static,
            new Ast\Statement\StaticStatementNode(
                array_values(array_map(
                    fn (Node\StaticVar $var): Ast\Statement\StaticVariableNode => $this->mapStaticVariable($var),
                    $static->vars,
                )),
            ),
        );
    }

    private function mapStaticVariable(Node\StaticVar $var): Ast\Statement\StaticVariableNode
    {
        return $this->applyAttributes(
            $var,
            new Ast\Statement\StaticVariableNode(
                $this->expressionMapper()->expectSimpleVariable($var->var),
                $var->default !== null ? $this->expressionMapper()->mapExpression($var->default) : null,
            ),
        );
    }

    private function mapUnsetStatement(Stmt\Unset_ $unset): Ast\Statement\UnsetStatementNode
    {
        return $this->applyAttributes(
            $unset,
            new Ast\Statement\UnsetStatementNode(
                array_values(array_map(fn (Expr $expr): Ast\ExpressionNode => $this->expressionMapper()->mapExpression($expr), $unset->vars)),
            ),
        );
    }

    private function mapDeclareStatement(Stmt\Declare_ $declare): Ast\Statement\DeclareStatementNode
    {
        $directives = array_values(array_map(
            fn (Stmt\DeclareDeclare $directive): Ast\Statement\DeclareDirectiveNode => $this->mapDeclareDirective($directive),
            $declare->declares,
        ));

        $blockStatements = $declare->stmts !== null ? $this->mapStatements($declare->stmts) : [];

        return $this->applyAttributes(
            $declare,
            new Ast\Statement\DeclareStatementNode(
                $directives,
                $blockStatements,
                null,
            ),
        );
    }

    private function mapDeclareDirective(Stmt\DeclareDeclare $directive): Ast\Statement\DeclareDirectiveNode
    {
        return $this->applyAttributes(
            $directive,
            new Ast\Statement\DeclareDirectiveNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($directive->key),
                $this->expressionMapper()->mapExpression($directive->value),
            ),
        );
    }

    private function mapGotoStatement(Stmt\Goto_ $goto): Ast\Statement\GotoStatementNode
    {
        return $this->applyAttributes(
            $goto,
            new Ast\Statement\GotoStatementNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($goto->name),
            ),
        );
    }

    private function mapLabelStatement(Stmt\Label $label): Ast\Statement\LabelStatementNode
    {
        return $this->applyAttributes(
            $label,
            new Ast\Statement\LabelStatementNode(
                $this->valueMapper->getTypeMapper()->mapIdentifier($label->name),
            ),
        );
    }

    private function mapInlineHtmlStatement(Stmt\InlineHTML $inlineHtml): Ast\Statement\InlineHtmlStatementNode
    {
        return $this->applyAttributes(
            $inlineHtml,
            new Ast\Statement\InlineHtmlStatementNode(
                $inlineHtml->value,
            ),
        );
    }

    private function mapHaltCompilerStatement(Stmt\HaltCompiler $haltCompiler): Ast\Statement\HaltCompilerStatementNode
    {
        return $this->applyAttributes(
            $haltCompiler,
            new Ast\Statement\HaltCompilerStatementNode(
                $haltCompiler->remaining,
            ),
        );
    }

    /**
     * @param Node\Stmt[] $statements
     */
    private function mapBlockStatement(array $statements, Node $original): Ast\Statement\BlockStatementNode
    {
        return $this->applyAttributes(
            $original,
            new Ast\Statement\BlockStatementNode(
                $this->mapStatements($statements),
            ),
        );
    }
}
