<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader;

use PhpParser\Parser;
use PhpParser\ParserFactory;
use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;
use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;
use PhpParser\PhpVersion as PhpParserPhpVersion;

final class NikicAstBuilder implements AstBuilder
{
    private readonly Parser $parser;

    public function __construct(
        private NikicToDomainNodeMapper $mapper,
        PhpVersion $phpVersion
    ) {
        $this->parser = (new ParserFactory())->createForVersion(match ($phpVersion) {
            PhpVersion::PHP_7_4 => PhpParserPhpVersion::fromString('7.4'),
            PhpVersion::PHP_8_0 => PhpParserPhpVersion::fromString('8.0'),
            PhpVersion::PHP_8_1 => PhpParserPhpVersion::fromString('8.1'),
            PhpVersion::PHP_8_2 => PhpParserPhpVersion::fromString('8.2'),
            PhpVersion::PHP_8_3 => PhpParserPhpVersion::fromString('8.3'),
            PhpVersion::PHP_8_4 => PhpParserPhpVersion::fromString('8.4'),
        });
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
