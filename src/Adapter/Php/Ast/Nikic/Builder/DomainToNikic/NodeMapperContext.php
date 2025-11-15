<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

interface NodeMapperContext
{
    public function expressionMapper(): ExpressionMapper;

    public function statementMapper(): StatementMapper;

    public function memberMapper(): MemberMapper;

    public function valueMapper(): ValueMapper;

    public function typeMapper(): TypeMapper;
}
