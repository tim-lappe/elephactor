<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class Psr4AutoloadMap
{
    /**
     * @var list<Psr4AutoloadMapItem> $items
     */
    private array $items = [];

    public function add(PhpNamespace $namespace, Directory $directory): void
    {
        $this->items[] = new Psr4AutoloadMapItem($namespace, $directory);
    }

    public function getItemForNamespace(PhpNamespace $namespace): Psr4AutoloadMapItem
    {
        foreach ($this->items as $key => $item) {
            if ($item->namespace()->equals($namespace)) {
                return $item;
            }
        }

        throw new \InvalidArgumentException(sprintf('Namespace %s not found', $namespace->name()));
    }

    public function getItemForDirectory(Directory $directory): Psr4AutoloadMapItem
    {
        foreach ($this->items as $key => $item) {
            if ($item->directory()->equals($directory)) {
                return $item;
            }
        }

        throw new \InvalidArgumentException(sprintf('Directory %s not found', $directory->name()));
    }

    public function resolveNamespaceForDirectory(Directory $directory): ?PhpNamespace
    {
        $currentNamespace = new PhpNamespace(new FullyQualifiedName([new Identifier($directory->name())]));
        $currentDirectory = $directory;

        while (true) {
            foreach ($this->items as $item) {
                if ($item->directory()->equals($currentDirectory)) {
                    return $currentNamespace->removeFirstPart()->prependNamespace($item->namespace());
                }
            }

            $parent = $currentDirectory->parent();
            if ($parent === null) {
                return null;
            }

            $currentNamespace = $currentNamespace->prepend(new Identifier($parent->name()));
            $currentDirectory = $parent;
        }
    }

    /**
     * @return array<Psr4AutoloadMapItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function printDebug(): void
    {
        foreach ($this->items as $item) {
            echo 'Directory: ' . $item->directory()->name() . ' Namespace: ' . $item->namespace()->name() . PHP_EOL;
        }
    }
}
