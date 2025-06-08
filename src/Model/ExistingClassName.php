<?php

namespace TimLappe\Elephactor\Model;

use TimLappe\Elephactor\Model\ClassName;

final class ExistingClassName
{
    public function __construct(
        private string $fullClassName,
    ) {
        $this->fullClassName = '\\' . trim($fullClassName, '\\');

        if (!class_exists($fullClassName)) {
            throw new \InvalidArgumentException(sprintf('Class %s not found', $fullClassName));
        }
    }

    public function getClassName(): ClassName
    {
        return new ClassName($this->getFullClassName());
    }

    public function getFullClassName(): string
    {
        return $this->fullClassName;
    }

    public function getShortClassName(): string
    {
        return substr($this->fullClassName, (int) strrpos($this->fullClassName, '\\') + 1);
    }

    public function __toString(): string
    {
        return $this->fullClassName;
    }
}