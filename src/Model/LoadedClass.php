<?php

namespace TimLappe\Elephactor\Model;

final class LoadedClass
{
    private RealFile $file;
    
    public function __construct(
        private ExistingClassName $className,
        private NamespaceDefinition $namespace,
    ) {

        $this->file = $this->namespace->getFileForClass($this->className);
    }

    public function getFile(): RealFile
    {
        return $this->file;
    }

    public function getExistingClassName(): ExistingClassName
    {
        return $this->className;
    }
    
    public function getNamespace(): NamespaceDefinition
    {
        return $this->namespace;
    }

    public function __toString(): string
    {
        return $this->namespace->getNamespace() . '\\' . $this->className->getShortClassName();
    }
}