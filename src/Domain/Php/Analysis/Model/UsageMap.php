<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Value\FullyQualifiedName;

final class UsageMap extends AbstractSemanticNode
{
    /**
     * @var list<UsageMapItem> $usages
     */
    private array $usages = [];

    public function addUsage(FullyQualifiedName $fullyQualifiedName, SemanticNode $referencedNode): void
    {
        $this->usages[] = new UsageMapItem($fullyQualifiedName, $referencedNode);
    }

    public function merge(UsageMap $usageMap): void
    {
        foreach ($usageMap->usages() as $usage) {
            $this->addUsage($usage->usedName(), $usage->referencedNode());
        }
    }

    /**
     * @return list<UsageMapItem>
     */
    public function get(FullyQualifiedName $fullyQualifiedName): array
    {
        $usages = [];
        foreach ($this->usages as $usage) {
            if ($usage->usedName()->equals($fullyQualifiedName)) {
                $usages[] = $usage;
            }
        }
        return $usages;
    }

    /**
     * @return list<UsageMapItem>
     */
    public function usages(): array
    {
        return $this->usages;
    }

    public function children(): array
    {
        return $this->usages;
    }

    public function __toString(): string
    {
        return 'UsageMap';
    }
}
