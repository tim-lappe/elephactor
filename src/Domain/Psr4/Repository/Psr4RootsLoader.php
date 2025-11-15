<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Repository;

use TimLappe\Elephactor\Domain\Psr4\Model\Psr4RootCollection;

interface Psr4RootsLoader
{
    public function load(): Psr4RootCollection;
}
