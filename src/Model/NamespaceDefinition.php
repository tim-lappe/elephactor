<?php

namespace TimLappe\Elephactor\Model;

final class NamespaceDefinition
{
    public function __construct(
        private string $namespace,
        private RealDirectory $dir,
    ) {
        $this->namespace = trim($namespace, '\\');
    }

    public function equalsNamespace(string $namespace): bool
    {
        $trimmedNamespace = trim($namespace, '\\');
        return $this->namespace === $trimmedNamespace;
    }

    public function contains(string $namespaceOrClassName): bool
    {
        $trimmedNamespace = trim($namespaceOrClassName, '\\');

        return str_starts_with($trimmedNamespace, $this->namespace);
    }

    public function getFileForClass(ExistingClassName $className): RealFile
    {
        if (!$this->contains($className->getFullClassName())) {
            throw new \InvalidArgumentException(sprintf(
                'Class %s is not in namespace %s',
                $className->getFullClassName(),
                $this->namespace
            ));
        }

        $relativePath = substr($className->getFullClassName(), strlen($this->namespace) + 1);
        return new RealFile($this->dir->getPath() . '/' . str_replace('\\', '/', $relativePath) . '.php');
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getDir(): RealDirectory
    {
        return $this->dir;
    }

    public function __toString(): string
    {
        return $this->namespace;
    }
}