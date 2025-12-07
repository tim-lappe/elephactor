<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister;

use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic\DomainToNikicNodeMapper;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;

final class NikicFilePersister implements PhpFilePersister
{
    public function __construct(
        private readonly DomainToNikicNodeMapper $nodeMapper,
    ) {
    }

    public function persist(PhpFile $phpFile): void
    {
        $statements = $this->nodeMapper->buildFile($phpFile->fileNode());

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $originalStatements = $parser->parse($phpFile->handle()->content());
        $originalTokens = $parser->getTokens();

        if ($originalStatements === null) {
            throw new \RuntimeException('Failed to parse file');
        }

        $prettyPrinter = new Standard();
        $content = $prettyPrinter->printFormatPreserving($statements, $originalStatements, $originalTokens);

        $phpFile->handle()->writeContent($content);
    }
}
