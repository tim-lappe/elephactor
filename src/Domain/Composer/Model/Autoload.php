<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Composer\Model;

use TimLappe\Elephactor\Domain\Psr4\Model\Psr4AutoloadMap;

final class Autoload
{
    public function __construct(
        private ?Psr4AutoloadMap $psr4AutoloadMap = null,
    ) {
    }

    public function psr4AutoloadMap(): ?Psr4AutoloadMap
    {
        return $this->psr4AutoloadMap;
    }
}
