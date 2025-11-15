<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;

interface StatementMapper
{
    /**
     * @param  list<StatementNode> $statements
     * @return list<Stmt>
     */
    public function buildStatements(array $statements): array;
}
