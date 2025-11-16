<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\ClassScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\Scope\MethodScope;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\UsageMap;
use TimLappe\Elephactor\Domain\Php\AST\Model\Declaration\MethodDeclarationNode;

final class SemanticMethodDecleration extends SemanticClassMember
{
    private UsageMap $usagesInMethod;

    public function __construct(
        ClassScope $classScope,
        MethodDeclarationNode $methodDeclarationNode,
    ) {

        parent::__construct($classScope, $methodDeclarationNode);
        $this->usagesInMethod = new UsageMap();
    }

    public function methodScope(): MethodScope
    {
        return new MethodScope($this, $this->classScope);
    }

    public function methodDeclarationNode(): MethodDeclarationNode
    {
        if (!$this->memberNode instanceof MethodDeclarationNode) {
            throw new \InvalidArgumentException('Method declaration node must be a MethodDeclarationNode');
        }

        return $this->memberNode;
    }

    public function usagesInMethod(): UsageMap
    {
        return $this->usagesInMethod;
    }

    public function children(): array
    {
        return [$this->usagesInMethod];
    }

    public function __toString(): string
    {
        return 'Method: ' . $this->methodDeclarationNode()->name()->__toString();
    }
}
