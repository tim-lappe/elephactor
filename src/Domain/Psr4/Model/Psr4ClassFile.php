<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\AST\Model\ClassLikeNode;

final class Psr4ClassFile extends PhpClassLike
{
    public function __construct(
        PhpFile $file,
    ) {
        $classLikeDeclaration = $file->fileNode()->classLikeDeclerations();
        if ($classLikeDeclaration->count() !== 1 || !$classLikeDeclaration->first() instanceof ClassLikeNode) {
            throw new \RuntimeException('Multiple class declarations not supported in file: ' . $file->handle()->name());
        }

        parent::__construct($file, $classLikeDeclaration->first());
    }
}
