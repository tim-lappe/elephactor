<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

final class Psr4RootCollection
{
    /**
     * @param array<Psr4Root> $psr4Roots
     */
    public function __construct(
        private array $psr4Roots,
    ) {
    }

    /**
     * @return array<Psr4Root>
     */
    public function roots(): array
    {
        return $this->psr4Roots;
    }

    public function add(Psr4Root $psr4Root): void
    {
        $this->psr4Roots[] = $psr4Root;
    }
}
