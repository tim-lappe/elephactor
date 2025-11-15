<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Repository\ClassProvider;
use TimLappe\Elephactor\Domain\Psr4\Repository\Psr4RootsLoader;

final class Psr4ClassProvider implements ClassProvider
{
    public function __construct(
        private readonly Psr4RootsLoader $psr4RootsLoader,
    ) {
    }

    public function provide(): PhpClassCollection
    {
        $classCollection = new PhpClassCollection([]);
        foreach ($this->psr4RootsLoader->load()->roots() as $psr4Root) {
            $classCollection->addAll($psr4Root->rootSegment()->nestedClasses());
        }
        return $classCollection;
    }
}
