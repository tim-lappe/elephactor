<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\ClassLike;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\Decleration\SemanticClassLikeDecleration;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

class PhpClassLike
{
    public function __construct(
        private readonly PhpFile $file,
        private readonly SemanticClassLikeDecleration $classLikeDeclaration,
    ) {
    }

    public function classLikeDeclaration(): SemanticClassLikeDecleration
    {
        return $this->classLikeDeclaration;
    }

    public function file(): PhpFile
    {
        return $this->file;
    }
}
