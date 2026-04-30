<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node\Stmt;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;

interface MemberMapper
{
    /**
     * @param  list<MemberNode> $members
     * @return list<Stmt>
     */
    public function buildMembers(array $members): array;
}
