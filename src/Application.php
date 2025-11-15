<?php

declare (strict_types=1);

namespace TimLappe\Elephactor;

use InvalidArgumentException;
use Symfony\Component\Console\Application as BaseApplication;
use TimLappe\Elephactor\Adapter\Composer\Psr4\ComposerPsr4RootsLoader;
use TimLappe\Elephactor\Adapter\Php\Ast\AstBuilder;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic\DomainToNikicNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain\NikicToDomainNodeMapper;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Loader\NikicAstBuilder;
use TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Persister\NikicClassPersister;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Model\PhpVersion;
use TimLappe\Elephactor\Command\Debug\DebugNamespaceTree;
use TimLappe\Elephactor\Command\Debug\DebugClassProvider;
use TimLappe\Elephactor\Command\RenameClass;
use TimLappe\Elephactor\Composer\ComposerJsonFileLoader;
use TimLappe\Elephactor\Domain\Php\Resolution\ClassFinder;
use TimLappe\Elephactor\Domain\Php\Repository\ChainedClassProvider;
use TimLappe\Elephactor\Domain\Php\Repository\ClassProvider;
use TimLappe\Elephactor\Domain\Psr4\Adapter\Psr4ClassProvider;
use TimLappe\Elephactor\Domain\Psr4\Repository\Psr4RootsLoader;
use TimLappe\Elephactor\Domain\Php\Refactoring\ChainedRefactoringExecutor;
use TimLappe\Elephactor\Domain\Php\Refactoring\Executors\ClassRenameExecutor;
use TimLappe\Elephactor\Domain\Php\Resolution\ClassReference\ClassReferenceFinder;

class Application extends BaseApplication
{
    private ClassProvider $classProvider;
    private ChainedRefactoringExecutor $refactoringExecutor;
    private AstBuilder $astBuilder;

    public function __construct(private ?Environment $environment = null, private ?Psr4RootsLoader $psr4RootsLoader = null)
    {
        parent::__construct('Elephactor', '1.0.0');

        $workingDirectory = getcwd();
        if ($workingDirectory === false) {
            throw new InvalidArgumentException('Could not determine working directory');
        }

        if ($this->environment === null) {
            $composerJson = (new ComposerJsonFileLoader($workingDirectory))->load();
            $this->environment = new Environment($workingDirectory, $composerJson->platformPhpVersion() ?? PhpVersion::fromHost(), $composerJson);
        }

        $this->astBuilder = new NikicAstBuilder(new NikicToDomainNodeMapper(), $this->environment->getTargetPhpVersion());

        if ($this->psr4RootsLoader === null) {
            $this->psr4RootsLoader = new ComposerPsr4RootsLoader($this->environment->getComposerJson(), $this->environment, $this->astBuilder);
        }

        $this->classProvider = new ChainedClassProvider([new Psr4ClassProvider($this->psr4RootsLoader)]);

        $this->refactoringExecutor = new ChainedRefactoringExecutor([new ClassRenameExecutor(new NikicClassPersister(new DomainToNikicNodeMapper()), new ClassReferenceFinder($this->classProvider->provide()))]);

        $this->setupCommands();
    }

    private function setupCommands(): void
    {
        if ($this->psr4RootsLoader === null) {
            throw new \RuntimeException('Psr4 roots loader is not set');
        }

        $this->add(new DebugNamespaceTree($this->psr4RootsLoader));
        $this->add(new DebugClassProvider());
        $this->add(new RenameClass());
    }

    public function getRefactoringExecutor(): ChainedRefactoringExecutor
    {
        return $this->refactoringExecutor;
    }

    public function getClassProvider(): ClassProvider
    {
        return $this->classProvider;
    }

    public function getClassFinder(): ClassFinder
    {
        return new ClassFinder($this->classProvider->provide());
    }

    public function getPsr4RootsLoader(): Psr4RootsLoader
    {
        if ($this->psr4RootsLoader === null) {
            throw new \RuntimeException('Psr4 roots loader is not set');
        }

        return $this->psr4RootsLoader;
    }

    public function getEnvironment(): Environment
    {
        if ($this->environment === null) {
            throw new \RuntimeException('Environment is not set');
        }

        return $this->environment;
    }
}
