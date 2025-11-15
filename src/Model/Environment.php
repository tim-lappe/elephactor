<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Model;

use TimLappe\Elephactor\Composer\ComposerJson;

final class Environment
{
    public function __construct(
        private string $projectRootAbsolutePath,
        private PhpVersion $targetPhpVersion,
        private ComposerJson $composerJson,
    ) {
        $realPath = realpath($this->projectRootAbsolutePath);
        if ($realPath === false || !is_dir($realPath)) {
            throw new \InvalidArgumentException(sprintf('Project root %s is not an accessible directory', $this->projectRootAbsolutePath));
        }
    }

    public function getProjectRootAbsolutePath(): string
    {
        return $this->projectRootAbsolutePath;
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
