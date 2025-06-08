<?php

namespace TimLappe\Elephactor\Loader;

use TimLappe\Elephactor\Model\NamespaceMap;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Model\NamespaceDefinition;
use TimLappe\Elephactor\Model\RealDirectory;

class NamespaceLoader
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function load(): NamespaceMap
    {
        $namespaceMap = $this->environment->getComposerJson()->getPsr4Autoload();
        $namespaces = [];
        
        foreach ($namespaceMap as $namespace => $path) {
            if (!is_string($namespace) || !is_string($path)) {
                throw new \InvalidArgumentException('Invalid namespace or path in composer.json');
            }

            $namespaces[$namespace] = new NamespaceDefinition($namespace, new RealDirectory($this->environment->getProjectRoot()->getPath() . '/' . $path));
        }

        return new NamespaceMap($namespaces);
    }
}