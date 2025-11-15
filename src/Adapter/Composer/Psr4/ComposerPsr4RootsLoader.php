<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Composer\Psr4;

use TimLappe\Elephactor\Adapter\Filesystem\NativeDirectoryHandle;
use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Composer\ComposerJson;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpClassCollection;
use TimLappe\Elephactor\Domain\Php\Model\DirectoryHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4NamespaceSegment;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4Root;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4RootCollection;
use TimLappe\Elephactor\Domain\Psr4\Repository\Psr4RootsLoader;

final class ComposerPsr4RootsLoader implements Psr4RootsLoader
{
    public function __construct(
        private readonly ComposerJson $composerJson,
        private readonly Environment $environment,
        private readonly AstBuilder $astBuilder,
    ) {
    }

    public function load(): Psr4RootCollection
    {
        $psr4Autoload = $this->composerJson->psr4Autoload();
        $psr4RootCollection = new Psr4RootCollection([]);

        foreach ($psr4Autoload as $namespace => $path) {
            if (!is_string($namespace) || !is_string($path)) {
                throw new \InvalidArgumentException('Invalid namespace or path in composer.json');
            }

            $dir = $this->environment->getProjectRootAbsolutePath() . '/' . $path;
            $nativeDirectoryHandle = new NativeDirectoryHandle($dir);
            $psr4RootCollection->add($this->createPsr4Root($namespace, $nativeDirectoryHandle));
        }

        return $psr4RootCollection;
    }

    private function createPsr4Root(string $namespace, DirectoryHandle $directoryHandle): Psr4Root
    {
        $rootSegment = Psr4NamespaceSegment::createFromQualifiedName($namespace);
        $leafSegment = $rootSegment->traverseToFirstLeafSegment();
        $leafSegment->handleBy($directoryHandle);

        $this->buildSegmentTree($leafSegment);

        return new Psr4Root($rootSegment);
    }

    private function buildSegmentTree(Psr4NamespaceSegment $currentSegment): void
    {
        if ($currentSegment->directoryHandle() === null) {
            throw new \RuntimeException('Current segment is not located');
        }

        $directoryHandle = $currentSegment->directoryHandle();
        if (!$directoryHandle instanceof NativeDirectoryHandle) {
            throw new \RuntimeException('Parent segment locator must be a FilesystemDirectoryNamespaceLocator');
        }

        $currentSegment->classes()->addAll($this->createClassCollection($directoryHandle, $currentSegment));

        $childDirectories = $directoryHandle->childDirectories();
        foreach ($childDirectories as $childDirectory) {
            $childSegment = $currentSegment->createChildSegmentByName($childDirectory->name());
            $childSegment->handleBy($childDirectory);

            $this->buildSegmentTree($childSegment);
        }
    }

    private function createClassCollection(NativeDirectoryHandle $directoryHandle, Psr4NamespaceSegment $namespaceSegment): PhpClassCollection
    {
        $classCollection = new PhpClassCollection([]);
        foreach ($directoryHandle->childFiles() as $file) {
            $fileNode = $this->astBuilder->build($file->readContent());
            $file = new PhpFile($file, $fileNode);
            $classCollection->add(
                new Psr4ClassFile(
                    $file,
                    $namespaceSegment,
                ),
            );
        }

        return $classCollection;
    }
}
