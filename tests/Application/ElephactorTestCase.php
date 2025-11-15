<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader\NikicAstBuilder;
use TimLappe\Elephactor\Application;
use TimLappe\Elephactor\Composer\ComposerJson;
use TimLappe\Elephactor\Debug\NamespaceSegmentPrinter;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4NamespaceSegment;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Model\PhpVersion;

abstract class ElephactorTestCase extends TestCase
{
    protected Environment $environment;
    protected PhpClassCollection $phpClassCollection;
    protected AstBuilder $astBuilder;

    private array $setupFiles = [];

    public function buildApplication(): Application
    {
        $this->environment = new Environment(
            getcwd(),
            PhpVersion::fromString('8.1'),
            new ComposerJson([
                'autoload' => [
                    'psr-4' => [
                        'VirtualTestNamespace\\' => 'virtual/',
                    ],
                ],
            ]),
        );

        $this->astBuilder = new NikicAstBuilder(new NikicToDomainNodeMapper(), $this->environment->getTargetPhpVersion());
        $this->phpClassCollection = new PhpClassCollection([]);

        $rootSegment = Psr4NamespaceSegment::createFromQualifiedName('VirtualTestNamespace');
        $this->buildTree($this->setupFiles, $rootSegment);

        return new Application($this->environment, new VirtualPsr4RootsLoader($this->phpClassCollection));
    }

    private function buildTree(array $currentTree, Psr4NamespaceSegment $currentSegment): array
    {
        foreach ($currentTree as $namespacePart => $namespacePartTree) {
            if (is_array($namespacePartTree)) {
                $childSegment = $currentSegment->createChildSegmentByName($namespacePart);
                $currentTree[$namespacePart] = $this->buildTree($namespacePartTree, $childSegment);
                continue;
            }

            if ($namespacePartTree instanceof VirtualFileHandle) {
                $fileNode = $this->astBuilder->build($namespacePartTree->readContent());
                $file = new PhpFile($namespacePartTree, $fileNode);
                $phpClass = new Psr4ClassFile($file, $currentSegment);

                $this->phpClassCollection->add($phpClass);
                $currentSegment->classes()->add($phpClass);
            }
        }

        return $currentTree;
    }

    protected function setupFile(array $namespaceParts, string $className, string $content): VirtualFileHandle
    {
        $fileHandle = new VirtualFileHandle($className, $content);

        $structure = [$className => $fileHandle];
        foreach (array_reverse($namespaceParts) as $namespacePart) {
            $structure = [$namespacePart => $structure];
        }

        $this->setupFiles = array_merge_recursive($this->setupFiles, $structure);

        return $fileHandle;
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