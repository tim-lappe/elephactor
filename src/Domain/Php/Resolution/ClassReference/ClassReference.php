<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Resolution\ClassReference;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;

final class ClassReference
{
    /**
     * @param list<QualifiedName> $referenceNodes
     */
    public function __construct(
        private readonly PhpFile $file,
        private readonly array $referenceNodes,
    ) {
    }

    public function file(): PhpFile
    {
        return $this->file;
    }

    /**
     * @return list<QualifiedName>
     */
    public function referenceNodes(): array
    {
        return $this->referenceNodes;
    }
}
