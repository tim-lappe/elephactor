<?php

namespace TimLappe\Elephactor\Composer;

use TimLappe\Elephactor\Model\PhpVersion;
use TimLappe\Elephactor\Model\RealDirectory;

class ComposerJson
{
    /**
     * @var array<mixed>
     */
    private array $composerJson;

    public function __construct(RealDirectory $projectRoot)
    {
        $content = file_get_contents($projectRoot->getPath() . '/composer.json');
        if ($content === false) {
            throw new \RuntimeException(sprintf('Could not read composer.json in %s. Please check if the file exists and is readable.', $projectRoot));
        }

        $composerJson = json_decode($content, true);
        if ($composerJson === null) {
            throw new \RuntimeException(sprintf('Could not parse composer.json in %s. Please check if the file exists and is valid.', $projectRoot));
        }

        if (!is_array($composerJson)) {
            throw new \RuntimeException(sprintf('Could not parse composer.json in %s. Please check if the file exists and is valid.', $projectRoot));
        }

        $this->composerJson = $composerJson;
    }
    
    /**
     * @return array<mixed>
     */
    public function getPsr4Autoload(): array
    {
        $autoloadSection = $this->composerJson['autoload'] ?? null;
        if (!is_array($autoloadSection) || !is_array($autoloadSection['psr-4'] ?? null)) {
            return [];
        }

        return $autoloadSection['psr-4'];
    }

    public function getPlatformPhpVersion(): ?PhpVersion
    {
        if (!is_array($this->composerJson['require'] ?? null)) {
            return null;
        }

        if (!is_string($this->composerJson['require']['php'] ?? null)) {
            return null;
        }

        return PhpVersion::fromString($this->composerJson['require']['php']);
    }
}