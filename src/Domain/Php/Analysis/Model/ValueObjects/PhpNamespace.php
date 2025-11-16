<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class PhpNamespace
{
    public function __construct(
        private readonly ?QualifiedName $name = null
    ) {
    }

    /**
     * @phpstan-assert-if-false !null $this->name()
     * @phpstan-assert-if-false !null $this->name
     * @phpstan-assert-if-true null $this->name()
     * @phpstan-assert-if-true null $this->name
     */
    public function isGlobal(): bool
    {
        return $this->name === null;
    }

    public function name(): ?QualifiedName
    {
        return $this->name;
    }

    public function contains(PhpNamespace $namespace): bool
    {
        if ($this->isGlobal() && $namespace->isGlobal()) {
            return true;
        }

        if ($this->isGlobal() && !$namespace->isGlobal()) {
            return true;
        }

        if (!$this->isGlobal() && $namespace->isGlobal()) {
            return false;
        }

        if (!$this->isGlobal() && !$namespace->isGlobal()) {
            return str_ends_with($this->name()->__toString(), $namespace->name()->__toString());
        }

        return false;
    }

    public function fullyQualifyName(QualifiedName|Identifier $name): FullyQualifiedName
    {
        if ($this->isGlobal()) {
            if ($name instanceof QualifiedName) {
                return new FullyQualifiedName($name->parts());
            }

            return new FullyQualifiedName([$name]);
        }

        if ($name instanceof QualifiedName) {
            return new FullyQualifiedName([...$this->name()->parts(), ...$name->parts()]);
        }

        return new FullyQualifiedName([...$this->name()->parts(), $name]);
    }

    public function equals(PhpNamespace $namespace): bool
    {
        if ($this->isGlobal() && $namespace->isGlobal()) {
            return true;
        }

        if ($this->isGlobal() || $namespace->isGlobal()) {
            return false;
        }

        return $this->name()->__toString() === $namespace->name()->__toString();
    }

    public function extend(QualifiedName $qualifiedName): PhpNamespace
    {
        if ($this->isGlobal()) {
            return new PhpNamespace($qualifiedName);
        }

        $currentName = $this->name();
        foreach ($qualifiedName->parts() as $part) {
            $currentName = $currentName->extend($part);
        }

        return new PhpNamespace($currentName);
    }

    public function prepend(Identifier $identifier): PhpNamespace
    {
        if ($this->name() === null) {
            return new PhpNamespace(new QualifiedName([$identifier]));
        }

        return new PhpNamespace($this->name()->prepend($identifier));
    }

    public function removeFirstPart(): PhpNamespace
    {
        if ($this->name() === null) {
            return new PhpNamespace(new QualifiedName([]));
        }

        return new PhpNamespace($this->name()->removeFirstPart());
    }

    public function prependNamespace(PhpNamespace $namespace): PhpNamespace
    {
        if ($this->name() === null) {
            return $namespace;
        }

        if ($namespace->name() === null) {
            return $this;
        }

        return new PhpNamespace(new QualifiedName([...$namespace->name()->parts(), ...$this->name()->parts()]));
    }
}
