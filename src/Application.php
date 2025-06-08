<?php

namespace TimLappe\Elephactor;

use InvalidArgumentException;
use Symfony\Component\Console\Application as BaseApplication;
use TimLappe\Elephactor\Composer\ComposerJson;
use TimLappe\Elephactor\Loader\ClassLoader;
use TimLappe\Elephactor\Loader\NamespaceLoader;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Model\PhpVersion;
use TimLappe\Elephactor\Model\RealDirectory;

class Application extends BaseApplication
{
    private Environment $environment;

    public function __construct(
        ?RealDirectory $projectRoot = null,
    )
    {
        $cwd = getcwd();
        if ($projectRoot === null && $cwd !== false) {
            $projectRoot = new RealDirectory($cwd);
        }

        if ($projectRoot === null) {
            throw new InvalidArgumentException('Could not determine project root');
        }

        $composerJson = new ComposerJson($projectRoot);
        $this->environment = new Environment($projectRoot, $composerJson->getPlatformPhpVersion() ?? PhpVersion::fromHost(), $composerJson);

        parent::__construct('Elephactor', '1.0.0');
    }

    public function getNamespaceLoader(): NamespaceLoader
    {
        return new NamespaceLoader($this->environment);
    }

    public function getClassLoader(): ClassLoader
    {
        return new ClassLoader($this->getNamespaceLoader()->load());
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}