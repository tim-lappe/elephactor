<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Element;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Declaration\MethodDeclarationNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\TypeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\MethodModifiers;

final class PhpMethod
{
    public function __construct(
        private readonly MethodDeclarationNode $node,
    ) {
    }

    public function name(): string
    {
        return $this->node->name()->value();
    }

    public function modifiers(): MethodModifiers
    {
        return $this->node->modifiers();
    }

    /**
     * @return list<PhpVariable>
     */
    public function parameters(): array
    {
        return array_map(
            static fn (ParameterNode $parameter): PhpVariable => new PhpVariable($parameter),
            $this->node->parameters(),
        );
    }

    public function returnType(): ?TypeNode
    {
        return $this->node->returnType();
    }

    public function docBlock(): ?DocBlock
    {
        return $this->node->docBlock();
    }

    public function returnsByReference(): bool
    {
        return $this->node->returnsByReference();
    }

    public function node(): MethodDeclarationNode
    {
        return $this->node;
    }
}
