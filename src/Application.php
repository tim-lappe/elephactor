<?php

declare (strict_types=1);

namespace TimLappe\Elephactor;

use InvalidArgumentException;
use Symfony\Component\Console\Application as BaseApplication;
use TimLappe\Elephactor\Adapter\Composer\ComposerConfigJsonLoader;
use TimLappe\Elephactor\Adapter\Composer\FsComposerProjectLoaderAdapter;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic\DomainToNikicNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader\NikicAstBuilder;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister\NikicFilePersister;
use TimLappe\Elephactor\Adapter\Workspace\FsDirectory;
use TimLappe\Elephactor\Adapter\Workspace\FsWorkspaceLoaderAdapter;
use TimLappe\Elephactor\Command\MoveClass;
use TimLappe\Elephactor\Command\RenameClass;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Psr4ClassLikeIndex;
use TimLappe\Elephactor\Domain\Php\Analysis\Analyser\FileAnalyser;
use TimLappe\Elephactor\Domain\Php\Refactoring\ChainedRefactoringExecutor;
use TimLappe\Elephactor\Domain\Php\Refactoring\Executors\ClassRenameExecutor;
use TimLappe\Elephactor\Domain\Php\Repository\PhpFileRepository;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Index\Psr4PhpFileIndex;
use TimLappe\Elephactor\Domain\Psr4\Refactoring\Executors\MoveFileExecutor;
use TimLappe\Elephactor\Domain\Workspace\Model\Workspace;

class Application extends BaseApplication
{
    private ChainedRefactoringExecutor $refactoringExecutor;
    private Workspace $workspace;

    public function __construct(?Workspace $workspace = null)
    {
        parent::__construct('Elephactor', '1.0.0');

        $this->workspace = $workspace ?? $this->setupWorkspace();

        $phpFilePersister = new NikicFilePersister(new DomainToNikicNodeMapper());

        $psr4FileIndex = $this->workspace->phpFileIndex()->getIndexForClass(Psr4PhpFileIndex::class);
        if (!$psr4FileIndex instanceof Psr4PhpFileIndex) {
            throw new \RuntimeException('Psr4 file index not found');
        }

        $this->refactoringExecutor = new ChainedRefactoringExecutor([
            new ClassRenameExecutor($phpFilePersister, $this->workspace),
            new MoveFileExecutor($psr4FileIndex, $phpFilePersister),
        ]);

        $this->setupCommands();
    }

    private function setupWorkspace(): Workspace
    {
        $workingDirectory = getcwd();
        if ($workingDirectory === false) {
            throw new InvalidArgumentException('Could not determine working directory');
        }

        $workspaceLoader = new FsWorkspaceLoaderAdapter();

        $fsDirectory = new FsDirectory($workingDirectory);
        $workspace = $workspaceLoader->load($fsDirectory);

        $this->setupComposerProject($workspace);

        return $workspace;
    }

    private function setupComposerProject(Workspace $workspace): void
    {
        $composerProjectLoader = new FsComposerProjectLoaderAdapter(new ComposerConfigJsonLoader());
        $composerProject = $composerProjectLoader->load($workspace->workspaceDirectory());

        if ($composerProject->composerConfig()->autoload()->psr4AutoloadMap() === null) {
            throw new \RuntimeException('Psr4 autoload map is not set');
        }

        $nikicAstBuilder = new NikicAstBuilder(new NikicToDomainNodeMapper(), $workspace->environment()->phpVersion());
        $fileAnalyser = FileAnalyser::createDefault();

        $psr4FileIndex = new Psr4PhpFileIndex(
            $composerProject->composerConfig()->autoload()->psr4AutoloadMap(),
            new PhpFileRepository($nikicAstBuilder, $fileAnalyser),
        );

        $psr4FileIndex->reload();

        $psr4ClassLikeIndex = new Psr4ClassLikeIndex($psr4FileIndex);
        $psr4ClassLikeIndex->reload();

        $workspace->registerClassLikeIndex($psr4ClassLikeIndex);
        $workspace->registerPhpFileIndex($psr4FileIndex);
    }

    private function setupCommands(): void
    {
        $this->add(new RenameClass());
        $this->add(new MoveClass());
    }

    public function refactoringExecutor(): ChainedRefactoringExecutor
    {
        return $this->refactoringExecutor;
    }

    public function workspace(): Workspace
    {
        return $this->workspace;
    }
}
