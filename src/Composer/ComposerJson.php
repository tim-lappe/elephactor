<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Composer;

use TimLappe\Elephactor\Model\PhpVersion;

class ComposerJson
{
    /**
     * @param array<mixed> $composerJson
     */
    public function __construct(
        private readonly array $composerJson,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function psr4Autoload(): array
    {
        $autoloadSection = $this->composerJson['autoload'] ?? null;
        if (!is_array($autoloadSection) || !is_array($autoloadSection['psr-4'] ?? null)) {
            return [];
        }

        return $autoloadSection['psr-4'];
    }

    public function platformPhpVersion(): ?PhpVersion
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
