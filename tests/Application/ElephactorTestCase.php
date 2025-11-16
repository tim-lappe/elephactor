<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use PHPUnit\Framework\TestCase;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader\NikicAstBuilder;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Domain\Php\Analysis\Analyser\FileAnalyser;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\ValueObjects\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Workspace\Model\Environment;
use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;
use TimLappe\Elephactor\Domain\Php\Repository\PhpFileRepository;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4PhpFileIndex;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Psr4ClassLikeIndex;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4AutoloadMap;
use TimLappe\Elephactor\Domain\Workspace\Model\Workspace;

abstract class ElephactorTestCase extends TestCase
{
    protected Workspace $workspace;
    protected VirtualDirectory $sourceDirectory;
    protected Application $application;

    public function setUp(): void
    {
        $workDir = new VirtualDirectory('workdir');

        $this->workspace = new Workspace(
            $workDir,
            new Environment(PhpVersion::fromString('8.3')),
        );

        $this->sourceDirectory = $workDir->createOrGetDirecotry('src');

        $psr4AutoloadMap = new Psr4AutoloadMap();
        $psr4AutoloadMap->add(new PhpNamespace(new QualifiedName([new Identifier('VirtualTestNamespace')])), $this->sourceDirectory);

        $nikicAstBuilder = new NikicAstBuilder(new NikicToDomainNodeMapper(), $this->workspace->environment()->phpVersion());

        $fileAnalyser = FileAnalyser::createDefault();
        $psr4FileIndex = new Psr4PhpFileIndex($psr4AutoloadMap, new PhpFileRepository($nikicAstBuilder, $fileAnalyser));
        $psr4FileIndex->reload();

        $this->workspace->registerPhpFileIndex($psr4FileIndex);
        $this->workspace->registerClassLikeIndex(new Psr4ClassLikeIndex($psr4FileIndex));
        $this->workspace->reloadIndices();

        $this->application = new Application($this->workspace);
    }

    protected function codeMatches(string $code, string $expectedCode): void
    {
        self::assertEquals($this->normalizeCode($expectedCode), $this->normalizeCode($code));
    }

    private function normalizeCode(string $code): string
    {
        while (strpos($code, "  ") !== false) {
            $code = str_replace("  ", " ", $code);
        }

        return $code;
    }
}
