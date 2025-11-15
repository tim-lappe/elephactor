<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Psr4\Model;

final class Psr4Root
{
    public function __construct(
        private Psr4NamespaceSegment $rootSegment,
    ) {
    }

    public function rootSegment(): Psr4NamespaceSegment
    {
        return $this->rootSegment;
    }
}
