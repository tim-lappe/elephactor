<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\Directory;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class Psr4AutoloadMap
{
    /**
     * @var list<Psr4AutoloadMapItem> $items
     */
    private array $items = [];

    public function add(QualifiedName $namespace, Directory $directory): void
    {
        $this->items[] = new Psr4AutoloadMapItem($namespace, $directory);
    }

    public function merge(Psr4AutoloadMap $autoloadMap): void
    {
        foreach ($autoloadMap->items as $item) {
            $this->add($item->namespace(), $item->directory());
        }
    }

    public function getItemForNamespace(QualifiedName $namespace): Psr4AutoloadMapItem
    {
        foreach ($this->items as $key => $item) {
            if ($item->namespace()->equals($namespace)) {
                return $item;
            }
        }

        throw new \InvalidArgumentException(sprintf('Namespace %s not found', $namespace->__toString()));
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

    public function resolveNamespaceForDirectory(Directory $directory): ?QualifiedName
    {
        $currentNamespace = new QualifiedName([new Identifier($directory->name())]);
        $currentDirectory = $directory;

        while (true) {
            foreach ($this->items as $item) {
                if ($item->directory()->equals($currentDirectory)) {
                    return $currentNamespace->removeFirstPart()->prepend($item->namespace()->lastPart());
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
            echo 'Directory: ' . $item->directory()->name() . ' Namespace: ' . $item->namespace()->__toString() . PHP_EOL;
        }
    }
}
