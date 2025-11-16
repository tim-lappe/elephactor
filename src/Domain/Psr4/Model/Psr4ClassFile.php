<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Model\ClassLike\PhpClassLike;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

final class Psr4ClassFile extends PhpClassLike
{
    public function __construct(
        PhpFile $file,
    ) {
        $classLikeDeclaration = $file->fileNode()->classLikeDeclarations();
        if (count($classLikeDeclaration) !== 1) {
            throw new \RuntimeException('Multiple class declarations not supported in file: ' . $file->handle()->name());
        }

        parent::__construct($file, $classLikeDeclaration[0]);
    }
}
