<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Composer\Model;

use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;

final class ComposerConfig
{
    public function __construct(
        private Autoload $autoload,
        private Autoload $autoloadDev,
        private ?PhpVersion $platformPhpVersion = null,
    ) {
    }

    public function autoload(): Autoload
    {
        return $this->autoload;
    }

    public function autoloadDev(): Autoload
    {
        return $this->autoloadDev;
    }

    public function platformPhpVersion(): ?PhpVersion
    {
        return $this->platformPhpVersion;
    }
}
