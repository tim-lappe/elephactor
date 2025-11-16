<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use PHPUnit\Framework\TestCase;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader\NikicAstBuilder;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Workspace\Model\Environment;
use TimLappe\Elephactor\Domain\Php\Model\PhpVersion;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4FileIndex;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Psr4ClassIndex;
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
        $psr4AutoloadMap->add(new PhpNamespace('VirtualTestNamespace'), $this->sourceDirectory);

        $psr4FileIndex = new Psr4FileIndex($psr4AutoloadMap);
        $psr4FileIndex->reload();

        $nikicAstBuilder = new NikicAstBuilder(new NikicToDomainNodeMapper(), $this->workspace->environment()->phpVersion());

        $this->workspace->registerFileIndex($psr4FileIndex);
        $this->workspace->registerClassIndex(new Psr4ClassIndex($psr4FileIndex, $nikicAstBuilder));
        $this->workspace->reloadIndices();

        $this->application = new Application($this->workspace);
    }

    protected function codeMatches(string $code, string $expectedCode): void
    {
        $this->assertEquals($this->normalizeCode($expectedCode), $this->normalizeCode($code));
    }

    private function normalizeCode(string $code): string
    {
        while (strpos($code, "  ") !== false) {
          $code = str_replace("  ", " ", $code);
        }

        return $code;
    }
}

