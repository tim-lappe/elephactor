<?php

namespace TimLappe\Elephactor\Model;

use TimLappe\Elephactor\Model\PhpVersion;
use TimLappe\Elephactor\Composer\ComposerJson;

final class Environment
{
    public function __construct(
        private RealDirectory $projectRoot,
        private PhpVersion $targetPhpVersion,
        private ComposerJson $composerJson,
    ) {
    }

    public function getProjectRoot(): RealDirectory
    {
        return $this->projectRoot;
    }

    public function getTargetPhpVersion(): PhpVersion
    {
        return $this->targetPhpVersion;
    }

    public function getComposerJson(): ComposerJson
    {
        return $this->composerJson;
    }
}