<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;

final class DomainToNikicNodeMapper implements NodeMapperContext
{
    private readonly ExpressionMapper $expressionMapper;
    private readonly StatementMapper $statementMapper;
    private readonly MemberMapper $memberMapper;
    private readonly ValueMapper $valueMapper;
    private readonly TypeMapper $typeMapper;

    public function __construct()
    {
        $this->typeMapper = new TypeMapper();
        $this->valueMapper = new ValueMapper($this->typeMapper, $this);
        $this->expressionMapper = new ExpressionMapper($this->valueMapper, $this->typeMapper, $this);
        $this->memberMapper = new NikicMemberMapper($this->expressionMapper, $this->valueMapper, $this->typeMapper, $this);
        $this->statementMapper = new NikicStatementMapper($this->expressionMapper, $this->valueMapper, $this->typeMapper, $this);
    }

    public function expressionMapper(): ExpressionMapper
    {
        return $this->expressionMapper;
    }

    public function statementMapper(): StatementMapper
    {
        return $this->statementMapper;
    }

    public function memberMapper(): MemberMapper
    {
        return $this->memberMapper;
    }

    public function valueMapper(): ValueMapper
    {
        return $this->valueMapper;
    }

    public function typeMapper(): TypeMapper
    {
        return $this->typeMapper;
    }

    /**
     * @return list<Stmt>
     */
    public function buildFile(Ast\FileNode $file): array
    {
        return $this->statementMapper->buildStatements($file->statements());
    }

    /**
     * @param  list<Ast\StatementNode> $statements
     * @return list<Stmt>
     */
    public function buildStatements(array $statements): array
    {
        return $this->statementMapper->buildStatements($statements);
    }

    /**
     * @param  list<Ast\MemberNode> $members
     * @return list<Stmt>
     */
    public function buildMembers(array $members): array
    {
        return $this->memberMapper->buildMembers($members);
    }

    public function buildExpression(Ast\ExpressionNode $expression): Expr
    {
        return $this->expressionMapper->buildExpression($expression);
    }
}
