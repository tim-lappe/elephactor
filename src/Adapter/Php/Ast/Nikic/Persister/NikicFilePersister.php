<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister;

use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister\FormatPreservingPrettyPrinter;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic\DomainToNikicNodeMapper;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Persister\PhpFilePersister;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;
use PhpParser\Node;

final class NikicFilePersister implements PhpFilePersister
{
    public function __construct(
        private readonly DomainToNikicNodeMapper $nodeMapper,
    ) {
    }

    public function persist(PhpFile $phpFile): void
    {
        $statements = $this->nodeMapper->buildFile($phpFile->fileNode());

        /**
         * @var array<Node> $originalStatements
         */
        $originalStatements = array_map(
            fn (Ast\Node $node) => $node->getAdapterNode(),
            $phpFile->fileNode()->children()->all()
        );

        $originalTokens = $phpFile->fileNode()->originalAdapterTokens();

        $prettyPrinter = new FormatPreservingPrettyPrinter();
        $content = $prettyPrinter->printFormatPreserving($statements, $originalStatements, $originalTokens);

        $phpFile->handle()->writeContent($content);
    }
}
