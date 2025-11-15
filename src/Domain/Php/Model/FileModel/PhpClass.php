<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\FullyQualifiedName;

class PhpClass
{
    public function __construct(
        private readonly PhpFile $file,
        private readonly PhpNamespace $namespace,
        private readonly ClassLikeNode $node,
    ) {
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function identifier(): Identifier
    {
        return $this->node->name();
    }

    public function changeIdentifier(Identifier $identifier): void
    {
        $this->node->changeName($identifier);
    }

    public function fullyQualifiedIdentifier(): FullyQualifiedName
    {
        return new FullyQualifiedName([...$this->namespace->parts(), $this->node->name()]);
    }

    public function file(): PhpFile
    {
        return $this->file;
    }
}
