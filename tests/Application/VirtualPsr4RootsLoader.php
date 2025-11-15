<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4NamespaceSegment;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4Root;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4RootCollection;
use TimLappe\Elephactor\Domain\Psr4\Repository\Psr4RootsLoader;

class VirtualPsr4RootsLoader implements Psr4RootsLoader
{   
    public function __construct(
        private readonly PhpClassCollection $phpClassCollection,
    ) {
    }

    public function load(): Psr4RootCollection
    {
        $psr4Root = Psr4NamespaceSegment::createFromQualifiedName('VirtualTestNamespace');
        $psr4Root->classes()->addAll($this->phpClassCollection);

        return new Psr4RootCollection([
            new Psr4Root($psr4Root),
        ]);
    }
}