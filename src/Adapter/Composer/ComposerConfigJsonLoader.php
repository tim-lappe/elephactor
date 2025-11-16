<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Composer;

use TimLappe\Elephactor\Adapter\Workspace\FsDirectory;
use TimLappe\Elephactor\Adapter\Workspace\FsFile;
use TimLappe\Elephactor\Domain\Composer\Model\Autoload;
use TimLappe\Elephactor\Domain\Composer\Model\ComposerConfig;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4AutoloadMap;

final class ComposerConfigJsonLoader
{
    public function load(FsFile $composerJsonFile): ComposerConfig
    {
        $content = $composerJsonFile->content();
        if ($content === '') {
            throw new \RuntimeException('Composer.json is empty');
        }

        $composerJson = json_decode($content, true);
        if ($composerJson === null) {
            throw new \RuntimeException('Composer.json is not valid JSON');
        }

        if (!is_array($composerJson)) {
            throw new \RuntimeException('Composer.json is not valid JSON');
        }

        return new ComposerConfig($this->loadAutoload($composerJson, $composerJsonFile));
    }

    /**
     * @param array<mixed> $composerJson
     */
    private function loadAutoload(array $composerJson, FsFile $composerJsonFile): Autoload
    {
        if (!is_array($composerJson['autoload'] ?? null)) {
            return new Autoload();
        }

        $psr4Autoload = $composerJson['autoload']['psr-4'] ?? null;
        if (!is_array($psr4Autoload)) {
            return new Autoload();
        }

        $psr4AutoloadMap = new Psr4AutoloadMap();
        foreach ($psr4Autoload as $namespace => $path) {
            if (!is_string($namespace) || !is_string($path)) {
                throw new \InvalidArgumentException('Invalid namespace or path in composer.json');
            }

            if (!is_dir($path)) {
                $path = $composerJsonFile->directory()->absolutePath() . '/' . $path;
                if (!is_dir($path)) {
                    throw new \InvalidArgumentException(sprintf('Path %s does not exist', $path));
                }
            }

            $psr4AutoloadMap->add(new PhpNamespace(QualifiedName::fromString($namespace)), new FsDirectory($path));
        }

        return new Autoload($psr4AutoloadMap);
    }
}
