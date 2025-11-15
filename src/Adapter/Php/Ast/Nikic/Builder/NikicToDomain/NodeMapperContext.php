<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

interface NodeMapperContext
{
    public function expressionMapper(): ExpressionMapper;

    public function statementMapper(): StatementMapper;

    public function memberMapper(): MemberMapper;
}
