<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\MoveClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Psr4\Refactoring\Commands\MoveClassFile;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualDirectory;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class MoveClassRefactoringsTest extends ElephactorTestCase
{
    public function testUpdatesNamespaceStatementAfterMove(): void
    {
        $testNamespaceDir = $this->sourceDirectory->createOrGetDirecotry('TestNamespace');
        $classFile = $testNamespaceDir->createFile('FooClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\TestNamespace;

        class FooClass
        {
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('NewNamespace')
            ->createOrGetDirecotry('SubNamespace');

        $this->workspace->reloadIndices();

        $this->moveClass('FooClass', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'FooClass.php');
        $this->assertNotNull($movedFile);
        if ($movedFile === null) {
            return;
        }

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\NewNamespace\SubNamespace;

        class FooClass
        {
        }
        PHP);
    }

    public function testRefactorsUseStatementsAcrossProject(): void
    {
        $testNamespaceDir = $this->sourceDirectory->createOrGetDirecotry('TestNamespace');
        $testNamespaceDir->createFile('FooClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\TestNamespace;

        class FooClass
        {
        }
        PHP);

        $testNamespaceDir->createFile('HelperClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\TestNamespace;

        class HelperClass
        {
        }
        PHP);

        $consumersDir = $this->sourceDirectory->createOrGetDirecotry('Consumers');
        $simpleConsumer = $consumersDir->createFile('SimpleConsumer.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumers;

        use VirtualTestNamespace\TestNamespace\FooClass;

        class SimpleConsumer
        {
            public function reference(): string
            {
                return FooClass::class;
            }
        }
        PHP);

        $groupConsumer = $consumersDir->createFile('GroupConsumer.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumers;

        use VirtualTestNamespace\TestNamespace\{FooClass, HelperClass};

        class GroupConsumer
        {
            public function make(): array
            {
                return [new FooClass(), new HelperClass()];
            }
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('NewNamespace')
            ->createOrGetDirecotry('Sub');

        $this->workspace->reloadIndices();

        $this->moveClass('FooClass', $targetDirectory);

        $this->codeMatches($simpleConsumer->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumers;

        use VirtualTestNamespace\NewNamespace\Sub\FooClass;

        class SimpleConsumer
        {
            public function reference(): string
            {
                return FooClass::class;
            }
        }
        PHP);

        $this->codeMatches($groupConsumer->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumers;

        use VirtualTestNamespace\{NewNamespace\Sub\FooClass, TestNamespace\HelperClass};

        class GroupConsumer
        {
            public function make(): array
            {
                return [new FooClass(), new HelperClass()];
            }
        }
        PHP);
    }

    private function moveClass(string $className, VirtualDirectory $targetDirectory): void
    {
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria($className));
        if ($class->first() === null) {
            $this->fail(sprintf('Class %s not found in workspace', $className));
        }

        $this->application
            ->refactoringExecutor()
            ->handle(new MoveClassFile($class->first(), $targetDirectory));
    }

    private function findFileIn(VirtualDirectory $directory, string $fileName): ?VirtualFile
    {
        return $directory
            ->childFiles()
            ->first(static fn (VirtualFile $file): bool => $file->name() === $fileName);
    }
}
