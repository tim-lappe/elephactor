<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Composer;

class ComposerJsonFileLoader
{
    public function __construct(
        private string $projectRootAbsolutePath
    ) {
        $realPath = realpath($this->projectRootAbsolutePath);
        if ($realPath === false || !is_dir($realPath)) {
            throw new \InvalidArgumentException(sprintf('Project root %s is not an accessible directory', $this->projectRootAbsolutePath));
        }
    }

    public function load(): ComposerJson
    {
        $content = file_get_contents($this->projectRootAbsolutePath . '/composer.json');
        if ($content === false) {
            throw new \RuntimeException(sprintf('Could not read composer.json in %s. Please check if the file exists and is readable.', $this->projectRootAbsolutePath));
        }

        $composerJson = json_decode($content, true);
        if ($composerJson === null) {
            throw new \RuntimeException(sprintf('Could not parse composer.json in %s. Please check if the file exists and is valid.', $this->projectRootAbsolutePath));
        }

        if (!is_array($composerJson)) {
            throw new \RuntimeException(sprintf('Could not parse composer.json in %s. Please check if the file exists and is valid.', $this->projectRootAbsolutePath));
        }

        return new ComposerJson($composerJson);
    }
}
