<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClass;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;

final class Psr4ClassFile extends PhpClass
{
    public function __construct(
        PhpFile $file,
        private PhpNamespace $namespace,
    ) {
        $className = str_replace('.php', '', $file->handle()->name());
        $className = str_replace('/', '\\', $className);
        $classLikeDeclaration = $file->fileNode()->findClassLikeDeclaration($className);

        if ($classLikeDeclaration === null) {
            throw new \RuntimeException('Class declaration not found in file: ' . $className . ' in namespace: ' . $namespace->name());
        }

        parent::__construct($file, $namespace, $classLikeDeclaration);
    }

    public function namespace(): PhpNamespace
    {
        return $this->namespace;
    }
}
