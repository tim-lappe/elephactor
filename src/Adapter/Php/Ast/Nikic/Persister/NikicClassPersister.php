<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister;

use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic\DomainToNikicNodeMapper;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Repository\PhpFilePersister;

final class NikicClassPersister implements PhpFilePersister
{
    private WhitespaceAwarePrettyPrinter $prettyPrinter;

    public function __construct(
        private readonly DomainToNikicNodeMapper $nodeMapper,
        ?WhitespaceAwarePrettyPrinter $prettyPrinter = null,
    ) {
        $this->prettyPrinter = $prettyPrinter ?? new WhitespaceAwarePrettyPrinter();
    }

    public function persist(PhpFile $phpFile): void
    {
        $statements = $this->nodeMapper->buildFile($phpFile->fileNode());
        $content = $this->prettyPrinter->prettyPrintFile($statements);

        $phpFile->handle()->writeContent($content);
    }
}
