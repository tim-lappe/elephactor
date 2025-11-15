<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\Model\DirectoryHandle;

final class Psr4NamespaceSegment
{
    /**
     * @param array<Psr4NamespaceSegment> $childSegments
     */
    public function __construct(
        private Psr4NamespaceSegmentIdentifier $identifier,
        private array $childSegments,
        private ?Psr4NamespaceSegment $parentSegment = null,
        private ?DirectoryHandle $directoryHandle = null,
        private PhpClassCollection $phpClassCollection = new PhpClassCollection([]),
    ) {
    }

    public static function createRootSegment(Psr4NamespaceSegmentIdentifier $identifier): self
    {
        return new self($identifier, [], null, null, new PhpClassCollection([]));
    }

    public static function createFromQualifiedName(string $qualifiedName): self
    {
        $qualifiedName = trim($qualifiedName, '\\');
        $segments = explode('\\', $qualifiedName);
        $rootSegment = self::createRootSegment(new Psr4NamespaceSegmentIdentifier($segments[0]));
        $currentSegment = $rootSegment;

        for ($i = 1; $i < count($segments); $i++) {
            $childSegment = new self(new Psr4NamespaceSegmentIdentifier($segments[$i]), [], $currentSegment);
            $currentSegment->addChildSegment($childSegment);
            $currentSegment = $childSegment;
        }

        return $rootSegment;
    }

    public function createChildSegmentByName(string $name): self
    {
        $childSegment = new self(new Psr4NamespaceSegmentIdentifier($name), [], $this);
        $this->addChildSegment($childSegment);
        return $childSegment;
    }

    public function handleBy(DirectoryHandle $directoryHandle): void
    {
        $this->directoryHandle = $directoryHandle;
    }

    public function classes(): PhpClassCollection
    {
        return $this->phpClassCollection;
    }

    public function nestedClasses(): PhpClassCollection
    {
        $classes = new PhpClassCollection([]);
        $classes->addAll($this->classes());

        foreach ($this->childSegments() as $childSegment) {
            $classes->addAll($childSegment->nestedClasses());
        }

        return $classes;
    }

    public function directoryHandle(): ?DirectoryHandle
    {
        return $this->directoryHandle;
    }

    public function traverseToFirstLeafSegment(): Psr4NamespaceSegment
    {
        if (count($this->childSegments()) === 0) {
            return $this;
        }

        $currentSegment = $this;
        foreach ($currentSegment->childSegments() as $childSegment) {
            $currentSegment = $childSegment->traverseToFirstLeafSegment();
        }

        return $currentSegment;
    }

    public function addChildSegment(Psr4NamespaceSegment $childSegment): void
    {
        $this->childSegments[] = $childSegment;
        $childSegment->parentSegment = $this;
    }

    /**
     * @return array<Psr4NamespaceSegment>
     */
    public function childSegments(): array
    {
        return $this->childSegments;
    }

    public function identifier(): Psr4NamespaceSegmentIdentifier
    {
        return $this->identifier;
    }

    public function fullyQualifiedIdentifier(): PhpNamespace
    {
        $fullyQualifiedName = $this->identifier()->name();
        $parentSegment = $this->parentSegment();
        while ($parentSegment !== null) {
            $fullyQualifiedName = $parentSegment->identifier()->name() . '\\' . $fullyQualifiedName;
            $parentSegment = $parentSegment->parentSegment();
        }

        return new PhpNamespace($fullyQualifiedName);
    }

    public function parentSegment(): ?Psr4NamespaceSegment
    {
        return $this->parentSegment;
    }
}
