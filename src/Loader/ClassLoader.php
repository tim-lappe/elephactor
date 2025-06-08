<?php

namespace TimLappe\Elephactor\Loader;

use TimLappe\Elephactor\Model\LoadedClass;
use TimLappe\Elephactor\Model\ExistingClassName;
use TimLappe\Elephactor\Model\NamespaceMap;

final class ClassLoader 
{
    public function __construct(
        private readonly NamespaceMap $namespaceMap
    ) {
    } 

    public function load(ExistingClassName $className): LoadedClass
    {
        $namespace = $this->namespaceMap->get($className->getFullClassName());
        if ($namespace === null) {
            throw new \InvalidArgumentException(sprintf('Class %s not found in any namespace: %s', $className, json_encode($this->namespaceMap)));
        }

        return new LoadedClass($className, $namespace);
    }
}