<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Repository;

use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Domain\Php\Analysis\Analyser\FileAnalyser;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class PhpFileRepository
{
    /**
     * @param list<PhpFile> $items
     */
    public function __construct(
        private readonly AstBuilder $astBuilder,
        private readonly FileAnalyser $fileAnalyser,
        private array $items = []
    ) {
    }

    public function find(File $file): PhpFile
    {
        foreach ($this->items as $item) {
            if ($item->handle()->equals($file)) {
                return $item;
            }
        }

        $fileNode = $this->astBuilder->build($file->content());
        $semanticFileNode = $this->fileAnalyser->analyse($fileNode);
        $phpFile = new PhpFile($file, $semanticFileNode);

        $this->add($phpFile);

        return $phpFile;
    }

    public function add(PhpFile $phpFile): void
    {
        $this->items[] = $phpFile;
    }
}
