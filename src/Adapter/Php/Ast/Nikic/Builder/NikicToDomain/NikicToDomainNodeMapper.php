<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use PhpParser\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast as Ast;

final class NikicToDomainNodeMapper implements NodeMapperContext
{
    private readonly StatementMapper $statementMapper;
    private readonly ExpressionMapper $expressionMapper;
    private readonly MemberMapper $memberMapper;
    private readonly TypeMapper $typeMapper;
    private readonly ValueMapper $valueMapper;

    public function __construct(
    ) {
        $this->typeMapper = new TypeMapper();
        $this->valueMapper = new ValueMapper($this->typeMapper, $this);
        $this->memberMapper = new MemberMapper($this->valueMapper, $this);
        $this->statementMapper = new StatementMapper($this->memberMapper, $this->valueMapper, $this);
        $this->expressionMapper = new ExpressionMapper($this->valueMapper, $this);
    }

    /**
     * @param  Node\Stmt[]             $statements
     * @return list<Ast\StatementNode>
     */
    public function mapStatements(array $statements): array
    {
        return $this->statementMapper->mapStatements($statements);
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
}
