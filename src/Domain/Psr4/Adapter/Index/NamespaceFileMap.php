<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Adapter\Index;

use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFileCollection;

final class NamespaceFileMap
{
    /**
     * @var list<NamespaceFileMapItem> $items
     */
    private array $items = [];

    public function add(PhpNamespace $namespace, PhpFileCollection $files): void
    {
        foreach ($this->items as $item) {
            if ($item->namespace()->equals($namespace)) {
                $item->files()->addAll($files);
                return;
            }
        }

        $this->items[] = new NamespaceFileMapItem($namespace, $files);
    }

    /**
     * @return list<NamespaceFileMapItem>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function getItemForNamespace(PhpNamespace $namespace): NamespaceFileMapItem
    {
        foreach ($this->items as $item) {
            if ($item->namespace()->equals($namespace)) {
                return $item;
            }
        }

        throw new \InvalidArgumentException(sprintf('Namespace %s not found', $namespace->name()));
    }
}
