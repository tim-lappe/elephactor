<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\ClassLike;

use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\AST\Analysis\FqnResolver;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

class PhpClassLike
{
    public function __construct(
        private readonly PhpFile $file,
        private readonly ClassLikeNode $classLikeNode,
    ) {
    }

    public function classLikeNode(): ClassLikeNode
    {
        return $this->classLikeNode;
    }

    public function fullyQualifiedName(): FullyQualifiedName
    {
        $fqnResolver = new FqnResolver($this->file->fileNode());
        return $fqnResolver->resolve($this->classLikeNode->name()->identifier());
    }

    public function file(): PhpFile
    {
        return $this->file;
    }
}
