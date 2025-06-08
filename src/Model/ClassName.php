<?php

namespace TimLappe\Elephactor\Model;

final class ClassName
{
    public function __construct(
        private string $fullClassName,
    ) {
        $this->fullClassName = trim($fullClassName, '\\');
    }

    public function getFullClassName(): string
    {
        return '\\' . $this->fullClassName;
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