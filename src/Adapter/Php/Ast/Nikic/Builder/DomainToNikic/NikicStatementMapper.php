<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\DeclareDirectiveNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\StaticVariableNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseClauseNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;

final class NikicStatementMapper implements StatementMapper
{
    public function __construct(
        private readonly ExpressionMapper $expressionMapper,
        private readonly ValueMapper $valueMapper,
        private readonly TypeMapper $typeMapper,
        private readonly NodeMapperContext $context,
    ) {
    }

    /**
     * @param  list<Ast\StatementNode> $statements
     * @return list<Stmt>
     */
    public function buildStatements(array $statements): array
    {
        $result = [];

        foreach ($statements as $statement) {
            foreach ($this->buildStatement($statement) as $built) {
                $result[] = $built;
            }
        }

        return $result;
    }

    /**
     * @return list<Stmt>
     */
    private function buildStatement(Ast\StatementNode $statement): array
    {
        return match (true) {
            $statement instanceof Ast\Statement\NamespaceDefinitionNode => [$this->buildNamespaceDefinition($statement)],
            $statement instanceof Ast\Statement\UseStatementNode => $this->buildUseStatement($statement),
            $statement instanceof Ast\Declaration\ConstDeclarationNode => [$this->buildConstDeclaration($statement)],
            $statement instanceof Ast\Declaration\FunctionDeclarationNode => [$this->buildFunctionDeclaration($statement)],
            $statement instanceof Ast\Declaration\ClassDeclarationNode => [$this->buildClassDeclaration($statement)],
            $statement instanceof Ast\Declaration\InterfaceDeclarationNode => [$this->buildInterfaceDeclaration($statement)],
            $statement instanceof Ast\Declaration\TraitDeclarationNode => [$this->buildTraitDeclaration($statement)],
            $statement instanceof Ast\Declaration\EnumDeclarationNode => [$this->buildEnumDeclaration($statement)],
            $statement instanceof Ast\Statement\ReturnStatementNode => [$this->buildReturnStatement($statement)],
            $statement instanceof Ast\Statement\BreakStatementNode => [$this->buildBreakStatement($statement)],
            $statement instanceof Ast\Statement\ContinueStatementNode => [$this->buildContinueStatement($statement)],
            $statement instanceof Ast\Statement\ExpressionStatementNode => [$this->buildExpressionStatement($statement)],
            $statement instanceof Ast\Statement\ThrowStatementNode => [$this->buildThrowStatement($statement)],
            $statement instanceof Ast\Statement\IfStatementNode => [$this->buildIfStatement($statement)],
            $statement instanceof Ast\Statement\WhileStatementNode => [$this->buildWhileStatement($statement)],
            $statement instanceof Ast\Statement\DoWhileStatementNode => [$this->buildDoWhileStatement($statement)],
            $statement instanceof Ast\Statement\ForStatementNode => [$this->buildForStatement($statement)],
            $statement instanceof Ast\Statement\ForeachStatementNode => [$this->buildForeachStatement($statement)],
            $statement instanceof Ast\Statement\SwitchStatementNode => [$this->buildSwitchStatement($statement)],
            $statement instanceof Ast\Statement\TryStatementNode => [$this->buildTryStatement($statement)],
            $statement instanceof Ast\Statement\BlockStatementNode => [$this->buildBlockStatement($statement)],
            $statement instanceof Ast\Statement\EchoStatementNode => [$this->buildEchoStatement($statement)],
            $statement instanceof Ast\Statement\GlobalStatementNode => [$this->buildGlobalStatement($statement)],
            $statement instanceof Ast\Statement\StaticStatementNode => [$this->buildStaticStatement($statement)],
            $statement instanceof Ast\Statement\UnsetStatementNode => [$this->buildUnsetStatement($statement)],
            $statement instanceof Ast\Statement\DeclareStatementNode => [$this->buildDeclareStatement($statement)],
            $statement instanceof Ast\Statement\GotoStatementNode => [$this->buildGotoStatement($statement)],
            $statement instanceof Ast\Statement\LabelStatementNode => [$this->buildLabelStatement($statement)],
            $statement instanceof Ast\Statement\InlineHtmlStatementNode => [$this->buildInlineHtmlStatement($statement)],
            $statement instanceof Ast\Statement\HaltCompilerStatementNode => [$this->buildHaltCompilerStatement($statement)],
            default => throw new \RuntimeException('Unsupported statement: ' . $statement::class),
        };
    }

    private function buildNamespaceDefinition(Ast\Statement\NamespaceDefinitionNode $statement): Stmt\Namespace_
    {
        $namespace = new Stmt\Namespace_(
            $this->valueMapper->buildQualifiedName($statement->name()->qualifiedName()),
            $this->buildStatements($statement->statements()),
        );

        $namespace->setAttribute(
            'kind',
            $statement->isBracketed() ? Stmt\Namespace_::KIND_BRACED : Stmt\Namespace_::KIND_SEMICOLON,
        );

        return $namespace;
    }

    /**
     * @return list<Stmt>
     */
    private function buildUseStatement(Ast\Statement\UseStatementNode $statement): array
    {
        $items = array_map(
            fn (UseClauseNode $clause): Node\UseItem => $this->buildUseClause($clause, $this->valueMapper->buildUseType($statement->useKind())),
            $statement->clauses(),
        );

        if ($statement->groupPrefix() !== null) {
            return [
                new Stmt\GroupUse(
                    $this->valueMapper->buildQualifiedName($statement->groupPrefix()->qualifiedName()),
                    $items,
                    $this->valueMapper->buildUseType($statement->useKind()),
                ),
            ];
        }

        return [
            new Stmt\Use_(
                $items,
                $this->valueMapper->buildUseType($statement->useKind()),
            ),
        ];
    }

    /**
     * @param int<1, 3> $type
     */
    private function buildUseClause(UseClauseNode $clause, int $type): Node\UseItem
    {
        return new Node\UseItem(
            $this->valueMapper->buildQualifiedName($clause->name()->qualifiedName()),
            $clause->alias() !== null ? $this->valueMapper->buildIdentifier($clause->alias()->identifier()) : null,
            $type,
        );
    }

    private function buildConstDeclaration(Ast\Declaration\ConstDeclarationNode $const): Stmt\Const_
    {
        $elements = array_map(
            fn (Ast\Declaration\ConstElementNode $element): Const_ => new Const_(
                $this->valueMapper->buildIdentifier($element->name()->identifier()),
                $this->expressionMapper->buildExpression($element->value()),
            ),
            $const->elements(),
        );

        return new Stmt\Const_($elements);
    }

    private function buildFunctionDeclaration(Ast\Declaration\FunctionDeclarationNode $function): Stmt\Function_
    {
        $node = new Stmt\Function_(
            $this->valueMapper->buildIdentifier($function->name()->identifier()),
            [
                'byRef' => $function->returnsByReference(),
                'params' => $this->expressionMapper->buildParameters($function->parameters()),
                'stmts' => $this->buildStatements($function->bodyStatements()),
                'returnType' => $this->typeMapper->buildType($function->returnType()),
                'attrGroups' => $this->valueMapper->buildAttributeGroups($function->attributes()),
            ],
        );

        $this->setDocComment($node, $function->docBlock());

        return $node;
    }

    private function buildClassDeclaration(Ast\Declaration\ClassDeclarationNode $class): Stmt\Class_
    {
        $node = new Stmt\Class_(
            $this->valueMapper->buildIdentifier($class->name()->identifier()),
            [
                'flags' => $this->valueMapper->buildClassFlags($class->modifiers()),
                'extends' => $class->extends() !== null ? $this->valueMapper->buildQualifiedName($class->extends()->qualifiedName()) : null,
                'implements' => array_map(
                    fn (Ast\Name\QualifiedNameNode $interface): Node\Name => $this->valueMapper->buildQualifiedName($interface->qualifiedName()),
                    $class->interfaces(),
                ),
                'stmts' => $this->context->memberMapper()->buildMembers($class->members()),
                'attrGroups' => $this->valueMapper->buildAttributeGroups($class->attributes()),
            ],
        );

        $this->setDocComment($node, $class->docBlock());

        return $node;
    }

    private function buildInterfaceDeclaration(Ast\Declaration\InterfaceDeclarationNode $interface): Stmt\Interface_
    {
        $node = new Stmt\Interface_(
            $this->valueMapper->buildIdentifier($interface->name()->identifier()),
            [
                'extends' => array_map(
                    fn (Ast\Name\QualifiedNameNode $parent): Node\Name => $this->valueMapper->buildQualifiedName($parent->qualifiedName()),
                    $interface->extends(),
                ),
                'stmts' => $this->context->memberMapper()->buildMembers($interface->members()),
                'attrGroups' => $this->valueMapper->buildAttributeGroups($interface->attributes()),
            ],
        );

        $this->setDocComment($node, $interface->docBlock());

        return $node;
    }

    private function buildTraitDeclaration(Ast\Declaration\TraitDeclarationNode $trait): Stmt\Trait_
    {
        $node = new Stmt\Trait_(
            $this->valueMapper->buildIdentifier($trait->name()->identifier()),
            [
                'stmts' => $this->context->memberMapper()->buildMembers($trait->members()),
                'attrGroups' => $this->valueMapper->buildAttributeGroups($trait->attributes()),
            ],
        );

        $this->setDocComment($node, $trait->docBlock());

        return $node;
    }

    private function buildEnumDeclaration(Ast\Declaration\EnumDeclarationNode $enum): Stmt\Enum_
    {
        $node = new Stmt\Enum_(
            $this->valueMapper->buildIdentifier($enum->name()->identifier()),
            [
                'scalarType' => $this->typeMapper->buildEnumScalarType($enum->scalarType()),
                'implements' => array_map(
                    fn (Ast\Name\QualifiedNameNode $interface): Node\Name => $this->valueMapper->buildQualifiedName($interface->qualifiedName()),
                    $enum->implements(),
                ),
                'stmts' => $this->context->memberMapper()->buildMembers($enum->members()),
                'attrGroups' => $this->valueMapper->buildAttributeGroups($enum->attributes()),
            ],
        );

        $this->setDocComment($node, $enum->docBlock());

        return $node;
    }

    private function buildReturnStatement(Ast\Statement\ReturnStatementNode $statement): Stmt\Return_
    {
        return new Stmt\Return_(
            $statement->expression() !== null ? $this->expressionMapper->buildExpression($statement->expression()) : null,
        );
    }

    private function buildBreakStatement(Ast\Statement\BreakStatementNode $statement): Stmt\Break_
    {
        return new Stmt\Break_(
            $statement->levels() !== null ? $this->expressionMapper->buildExpression($statement->levels()) : null,
        );
    }

    private function buildContinueStatement(Ast\Statement\ContinueStatementNode $statement): Stmt\Continue_
    {
        return new Stmt\Continue_(
            $statement->levels() !== null ? $this->expressionMapper->buildExpression($statement->levels()) : null,
        );
    }

    private function buildExpressionStatement(Ast\Statement\ExpressionStatementNode $statement): Stmt\Expression
    {
        return new Stmt\Expression($this->expressionMapper->buildExpression($statement->expression()));
    }

    private function buildThrowStatement(Ast\Statement\ThrowStatementNode $statement): Stmt\Expression
    {
        return new Stmt\Expression(
            new Expr\Throw_($this->expressionMapper->buildExpression($statement->expression())),
        );
    }

    private function buildIfStatement(Ast\Statement\IfStatementNode $statement): Stmt\If_
    {
        return new Stmt\If_(
            $this->expressionMapper->buildExpression($statement->condition()),
            [
                'stmts' => $this->buildStatements($statement->ifStatements()),
                'elseifs' => array_map(
                    fn (Ast\Statement\ElseIfClauseNode $clause): Stmt\ElseIf_ => $this->buildElseIfClause($clause),
                    $statement->elseIfClauses(),
                ),
                'else' => $statement->elseClause() !== null ? $this->buildElseClause($statement->elseClause()) : null,
            ],
        );
    }

    private function buildElseIfClause(Ast\Statement\ElseIfClauseNode $clause): Stmt\ElseIf_
    {
        return new Stmt\ElseIf_(
            $this->expressionMapper->buildExpression($clause->condition()),
            $this->buildStatements($clause->statements()),
        );
    }

    private function buildElseClause(Ast\Statement\ElseClauseNode $clause): Stmt\Else_
    {
        return new Stmt\Else_($this->buildStatements($clause->statements()));
    }

    private function buildWhileStatement(Ast\Statement\WhileStatementNode $statement): Stmt\While_
    {
        return new Stmt\While_(
            $this->expressionMapper->buildExpression($statement->condition()),
            $this->buildStatements($statement->statements()),
        );
    }

    private function buildDoWhileStatement(Ast\Statement\DoWhileStatementNode $statement): Stmt\Do_
    {
        return new Stmt\Do_(
            $this->expressionMapper->buildExpression($statement->condition()),
            $this->buildStatements($statement->statements()),
        );
    }

    private function buildForStatement(Ast\Statement\ForStatementNode $statement): Stmt\For_
    {
        return new Stmt\For_([
            'init' => array_map(
                fn (Ast\ExpressionNode $expr): Expr => $this->expressionMapper->buildExpression($expr),
                $statement->initializers(),
            ),
            'cond' => array_map(
                fn (Ast\ExpressionNode $expr): Expr => $this->expressionMapper->buildExpression($expr),
                $statement->conditions(),
            ),
            'loop' => array_map(
                fn (Ast\ExpressionNode $expr): Expr => $this->expressionMapper->buildExpression($expr),
                $statement->loopExpressions(),
            ),
            'stmts' => $this->buildStatements($statement->statements()),
        ]);
    }

    private function buildForeachStatement(Ast\Statement\ForeachStatementNode $statement): Stmt\Foreach_
    {
        return new Stmt\Foreach_(
            $this->expressionMapper->buildExpression($statement->source()),
            $this->expressionMapper->buildExpression($statement->value()),
            [
                'keyVar' => $statement->key() !== null ? $this->expressionMapper->buildExpression($statement->key()) : null,
                'byRef' => $statement->iteratesByReference(),
                'stmts' => $this->buildStatements($statement->statements()),
            ],
        );
    }

    private function buildSwitchStatement(Ast\Statement\SwitchStatementNode $statement): Stmt\Switch_
    {
        return new Stmt\Switch_(
            $this->expressionMapper->buildExpression($statement->expression()),
            array_map(
                fn (Ast\Statement\SwitchCaseNode $case): Stmt\Case_ => $this->buildSwitchCase($case),
                $statement->cases(),
            ),
        );
    }

    private function buildSwitchCase(Ast\Statement\SwitchCaseNode $case): Stmt\Case_
    {
        return new Stmt\Case_(
            $case->condition() !== null ? $this->expressionMapper->buildExpression($case->condition()) : null,
            $this->buildStatements($case->statements()),
        );
    }

    private function buildTryStatement(Ast\Statement\TryStatementNode $statement): Stmt\TryCatch
    {
        return new Stmt\TryCatch(
            $this->buildStatements($statement->tryStatements()),
            array_map(
                fn (Ast\Statement\CatchClauseNode $clause): Stmt\Catch_ => $this->buildCatchClause($clause),
                $statement->catchClauses(),
            ),
            $statement->finallyClause() !== null ? $this->buildFinallyClause($statement->finallyClause()) : null,
        );
    }

    private function buildCatchClause(Ast\Statement\CatchClauseNode $clause): Stmt\Catch_
    {
        $types = array_map(
            function (Ast\TypeNode $type): Node\Name {
                $built = $this->typeMapper->buildType($type);
                if (!$built instanceof Node\Name) {
                    throw new \RuntimeException('Catch clause requires named types');
                }

                return $built;
            },
            $clause->types(),
        );

        return new Stmt\Catch_(
            $types,
            new Expr\Variable($clause->variable()->value()),
            $this->buildStatements($clause->statements()),
        );
    }

    private function buildFinallyClause(Ast\Statement\FinallyClauseNode $clause): Stmt\Finally_
    {
        return new Stmt\Finally_($this->buildStatements($clause->statements()));
    }

    private function buildBlockStatement(Ast\Statement\BlockStatementNode $statement): Stmt\Block
    {
        return new Stmt\Block($this->buildStatements($statement->statements()));
    }

    private function buildEchoStatement(Ast\Statement\EchoStatementNode $statement): Stmt\Echo_
    {
        return new Stmt\Echo_(
            array_map(
                fn (Ast\ExpressionNode $expr): Expr => $this->expressionMapper->buildExpression($expr),
                $statement->expressions(),
            ),
        );
    }

    private function buildGlobalStatement(Ast\Statement\GlobalStatementNode $statement): Stmt\Global_
    {
        return new Stmt\Global_(
            array_map(
                fn (Ast\ExpressionNode $expr): Expr => $this->expressionMapper->buildExpression($expr),
                $statement->variables(),
            ),
        );
    }

    private function buildStaticStatement(Ast\Statement\StaticStatementNode $statement): Stmt\Static_
    {
        return new Stmt\Static_(
            array_map(
                fn (StaticVariableNode $variable): Node\StaticVar => $this->buildStaticVariable($variable),
                $statement->variables(),
            ),
        );
    }

    private function buildStaticVariable(StaticVariableNode $variable): Node\StaticVar
    {
        return new Node\StaticVar(
            new Expr\Variable($variable->name()->value()),
            $variable->defaultValue() !== null ? $this->expressionMapper->buildExpression($variable->defaultValue()) : null,
        );
    }

    private function buildUnsetStatement(Ast\Statement\UnsetStatementNode $statement): Stmt\Unset_
    {
        return new Stmt\Unset_(
            array_map(
                fn (Ast\ExpressionNode $expr): Expr => $this->expressionMapper->buildExpression($expr),
                $statement->expressions(),
            ),
        );
    }

    private function buildDeclareStatement(Ast\Statement\DeclareStatementNode $statement): Stmt\Declare_
    {
        $stmts = null;
        if ($statement->blockStatements() !== []) {
            $stmts = $this->buildStatements($statement->blockStatements());
        } elseif ($statement->singleStatement() !== null) {
            $stmts = $this->buildStatements([$statement->singleStatement()]);
        }

        return new Stmt\Declare_(
            $this->buildDeclareDirectives($statement->directives()),
            $stmts,
        );
    }

    /**
     * @param  list<DeclareDirectiveNode> $directives
     * @return list<Node\DeclareItem>
     */
    private function buildDeclareDirectives(array $directives): array
    {
        return array_map(
            fn (DeclareDirectiveNode $directive): Node\DeclareItem => $this->buildDeclareDirective($directive),
            $directives,
        );
    }

    private function buildDeclareDirective(DeclareDirectiveNode $directive): Node\DeclareItem
    {
        return new Node\DeclareItem(
            $this->valueMapper->buildIdentifier($directive->name()->identifier()),
            $this->expressionMapper->buildExpression($directive->value()),
        );
    }

    private function buildGotoStatement(Ast\Statement\GotoStatementNode $statement): Stmt\Goto_
    {
        return new Stmt\Goto_($this->valueMapper->buildIdentifier($statement->label()->identifier()));
    }

    private function buildLabelStatement(Ast\Statement\LabelStatementNode $statement): Stmt\Label
    {
        return new Stmt\Label($this->valueMapper->buildIdentifier($statement->label()));
    }

    private function buildInlineHtmlStatement(Ast\Statement\InlineHtmlStatementNode $statement): Stmt\InlineHTML
    {
        return new Stmt\InlineHTML($statement->content());
    }

    private function buildHaltCompilerStatement(Ast\Statement\HaltCompilerStatementNode $statement): Stmt\HaltCompiler
    {
        return new Stmt\HaltCompiler($statement->remainingContent());
    }

    private function setDocComment(Node $node, ?DocBlock $docBlock): void
    {
        $doc = $this->valueMapper->buildDocBlock($docBlock);
        if ($doc !== null) {
            $node->setDocComment($doc);
        }
    }
}
