<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\MoveClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\MoveFile;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualDirectory;
use TimLappe\ElephactorTests\Application\VirtualFile;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Workspace\Model\Filesystem\File;

final class MoveClassEdgeCasesTest extends ElephactorTestCase
{
    public function testUpdatesComplexClassReferencesAcrossExpressionsAndTypes(): void
    {
        $domainDir = $this->sourceDirectory->createOrGetDirecotry('Domain');
        $domainDir->createFile('TargetClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Domain;

        #[\Attribute]
        final class TargetClass
        {
            public const CONSTANT = 'initial';

            public static int $counter = 0;

            public static function create(): self
            {
                return new self();
            }
        }
        PHP);

        $domainDir->createFile('HelperClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Domain;

        final class HelperClass
        {
        }
        PHP);

        $consumersDir = $this->sourceDirectory->createOrGetDirecotry('Consumers');
        $complexUsage = $consumersDir->createFile('ComplexUsage.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumers;

        use VirtualTestNamespace\Domain\{HelperClass, TargetClass};
        use VirtualTestNamespace\Domain\TargetClass as ImportedAlias;

        #[TargetClass]
        final class ComplexUsage extends TargetClass
        {
            public function __construct(private TargetClass $typedProperty, private HelperClass|TargetClass $union)
            {
            }

            public function aliasUsage(): ImportedAlias
            {
                return new ImportedAlias();
            }

            public function build(TargetClass $parameter): TargetClass
            {
                $instance = new TargetClass();
                TargetClass::create();
                TargetClass::$counter++;
                $value = TargetClass::CONSTANT;

                if ($instance instanceof TargetClass) {
                    return $instance;
                }

                \VirtualTestNamespace\Domain\TargetClass::create();

                return new class($parameter) extends TargetClass {
                    public function __construct(private TargetClass $inner)
                    {
                    }

                    public function descriptor(): string
                    {
                        return TargetClass::class;
                    }
                };
            }
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('Refactored')
            ->createOrGetDirecotry('Core');

        $this->workspace->reloadIndices();

        $this->moveClass('TargetClass', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'TargetClass.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Refactored\Core;

        #[\Attribute]
        final class TargetClass
        {
            public const CONSTANT = 'initial';

            public static int $counter = 0;

            public static function create(): self
            {
                return new self();
            }
        }
        PHP);

        $this->codeMatches($complexUsage->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumers;

        use VirtualTestNamespace\{Domain\HelperClass, Refactored\Core\TargetClass};
        use VirtualTestNamespace\Refactored\Core\TargetClass as ImportedAlias;

        #[TargetClass]
        final class ComplexUsage extends TargetClass
        {
            public function __construct(private TargetClass $typedProperty, private HelperClass|TargetClass $union)
            {
            }

            public function aliasUsage(): ImportedAlias
            {
                return new ImportedAlias();
            }

            public function build(TargetClass $parameter): TargetClass
            {
                $instance = new TargetClass();
                TargetClass::create();
                TargetClass::$counter++;
                $value = TargetClass::CONSTANT;

                if ($instance instanceof TargetClass) {
                    return $instance;
                }

                \VirtualTestNamespace\Refactored\Core\TargetClass::create();

                return new class($parameter) extends TargetClass
                {
                    public function __construct(private TargetClass $inner)
                    {
                    }

                    public function descriptor(): string
                    {
                        return TargetClass::class;
                    }
                };
            }
        }
        PHP);
    }

    public function testUpdatesInterfacesAcrossImplementationsAnonymousClassesAndEnums(): void
    {
        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $contractsDir->createFile('FooContract.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface FooContract
        {
            public function run(): void;
        }
        PHP);

        $extensionsDir = $this->sourceDirectory->createOrGetDirecotry('Extensions');
        $childContract = $extensionsDir->createFile('ChildContract.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Extensions;

        use VirtualTestNamespace\Contracts\FooContract;

        interface ChildContract extends FooContract
        {
        }
        PHP);

        $servicesDir = $this->sourceDirectory->createOrGetDirecotry('Services');
        $implementsContract = $servicesDir->createFile('ImplementsContract.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        use VirtualTestNamespace\Contracts\FooContract;

        final class ImplementsContract implements FooContract
        {
            public function __construct(private FooContract $contract)
            {
            }

            public function transform(FooContract $input): FooContract
            {
                if ($input instanceof FooContract) {
                    return $input;
                }

                return new class($input) implements FooContract
                {
                    public function __construct(private FooContract $decorated)
                    {
                    }

                    public function run(): void
                    {
                        $this->decorated->run();
                    }
                };
            }

            public function run(): void
            {
                $this->contract->run();
            }
        }
        PHP);

        $processorFile = $servicesDir->createFile('Processor.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        use VirtualTestNamespace\Contracts\FooContract;

        final class Processor
        {
            public function __construct(private FooContract $contract)
            {
            }

            public function execute(FooContract $argument): FooContract
            {
                if ($argument instanceof FooContract) {
                    return $argument;
                }

                return $this->contract;
            }
        }
        PHP);

        $stateDir = $this->sourceDirectory->createOrGetDirecotry('State');
        $enumFile = $stateDir->createFile('WorkflowStatus.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\State;

        use VirtualTestNamespace\Contracts\FooContract;

        enum WorkflowStatus implements FooContract
        {
            case STARTED;

            public function run(): void
            {
            }
        }
        PHP);

        $targetDirectory = $this->sourceDirectory->createOrGetDirecotry('Protocols');

        $this->workspace->reloadIndices();

        $this->moveClass('FooContract', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'FooContract.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Protocols;

        interface FooContract
        {
            public function run(): void;
        }
        PHP);

        $this->codeMatches($childContract->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Extensions;

        use VirtualTestNamespace\Protocols\FooContract;

        interface ChildContract extends FooContract
        {
        }
        PHP);

        $this->codeMatches($implementsContract->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        use VirtualTestNamespace\Protocols\FooContract;

        final class ImplementsContract implements FooContract
        {
            public function __construct(private FooContract $contract)
            {
            }

            public function transform(FooContract $input): FooContract
            {
                if ($input instanceof FooContract) {
                    return $input;
                }

                return new class($input) implements FooContract
                {
                    public function __construct(private FooContract $decorated)
                    {
                    }

                    public function run(): void
                    {
                        $this->decorated->run();
                    }
                };
            }

            public function run(): void
            {
                $this->contract->run();
            }
        }
        PHP);

        $this->codeMatches($processorFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        use VirtualTestNamespace\Protocols\FooContract;

        final class Processor
        {
            public function __construct(private FooContract $contract)
            {
            }

            public function execute(FooContract $argument): FooContract
            {
                if ($argument instanceof FooContract) {
                    return $argument;
                }

                return $this->contract;
            }
        }
        PHP);

        $this->codeMatches($enumFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\State;

        use VirtualTestNamespace\Protocols\FooContract;

        enum WorkflowStatus implements FooContract
        {
            case STARTED;

            public function run(): void
            {
            }
        }
        PHP);
    }

    public function testUpdatesTraitUseAliasAndPrecedenceAdaptations(): void
    {
        $behaviorDir = $this->sourceDirectory->createOrGetDirecotry('Behavior');
        $behaviorDir->createFile('SharedTrait.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Behavior;

        trait SharedTrait
        {
            public function helper(): string
            {
                return 'shared';
            }
        }
        PHP);

        $behaviorDir->createFile('CompetingTrait.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Behavior;

        trait CompetingTrait
        {
            public function helper(): string
            {
                return 'competing';
            }
        }
        PHP);

        $servicesDir = $this->sourceDirectory->createOrGetDirecotry('Services');
        $consumerFile = $servicesDir->createFile('TraitConsumer.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        use VirtualTestNamespace\Behavior\{CompetingTrait, SharedTrait};

        final class TraitConsumer
        {
            use SharedTrait;

            use SharedTrait {
                SharedTrait::helper as sharedAlias;
            }

            use SharedTrait, CompetingTrait {
                SharedTrait::helper insteadof CompetingTrait;
                CompetingTrait::helper as competingAlias;
            }

            use CompetingTrait, SharedTrait {
                CompetingTrait::helper insteadof SharedTrait;
            }
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('Refactored')
            ->createOrGetDirecotry('Mixins');

        $this->workspace->reloadIndices();

        $this->moveClass('SharedTrait', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'SharedTrait.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Refactored\Mixins;

        trait SharedTrait
        {
            public function helper(): string
            {
                return 'shared';
            }
        }
        PHP);

        $this->codeMatches($consumerFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        use VirtualTestNamespace\{Behavior\CompetingTrait, Refactored\Mixins\SharedTrait};

        final class TraitConsumer
        {
            use SharedTrait;

            use SharedTrait {
                SharedTrait::helper as sharedAlias;
            }

            use SharedTrait, CompetingTrait {
                SharedTrait::helper insteadof CompetingTrait;
                CompetingTrait::helper as competingAlias;
            }

            use CompetingTrait, SharedTrait {
                CompetingTrait::helper insteadof SharedTrait;
            }
        }
        PHP);
    }

    private function moveClass(string $className, VirtualDirectory $targetDirectory): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier($className)))->first();
        if ($class === null) {
            self::fail(sprintf('Class %s not found in workspace', $className));
        }

        if (!$class instanceof Psr4ClassFile) {
            self::fail(sprintf('Class %s is not a Psr4ClassFile', $className));
        }

        $this->application
            ->refactoringExecutor()
            ->handle(new MoveFile($class->file(), $targetDirectory));
    }

    private function findFileIn(VirtualDirectory $directory, string $fileName): ?File
    {
        return $directory
            ->childFiles()
            ->first(static fn (VirtualFile $file): bool => $file->name() === $fileName);
    }
}
