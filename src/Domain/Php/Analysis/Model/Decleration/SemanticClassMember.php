<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\AbstractSemanticNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;

abstract class SemanticClassMember extends AbstractSemanticNode
{
    public function __construct(
        protected readonly ClassScope $classScope,
        protected readonly MemberNode $memberNode,
    ) {
    }

    public function astNode(): MemberNode
    {
        return $this->memberNode;
    }
}
