<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader;

use PhpParser\Parser;
use PhpParser\ParserFactory;
use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\FileNode;
use TimLappe\Elephactor\Model\PhpVersion;

final class NikicAstBuilder implements AstBuilder
{
    private readonly Parser $parser;

    public function __construct(
        private NikicToDomainNodeMapper $mapper,
        PhpVersion $targetVersion
    ) {
        $this->parser = (new ParserFactory())->createForVersion($targetVersion->toNikicPhpParserVersion());
    }

    public function build(string $content): FileNode
    {
        $statements = $this->parser->parse($content);
        if ($statements === null) {
            throw new \RuntimeException('Failed to parse file');
        }

        $astStatements = $this->mapper->mapStatements($statements);
        return new FileNode($astStatements);
    }
}
