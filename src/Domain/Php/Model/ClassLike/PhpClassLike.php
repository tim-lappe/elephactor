<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\ClassLike;

use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

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

    public function file(): PhpFile
    {
        return $this->file;
    }
}
