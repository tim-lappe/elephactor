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
        self::assertNotNull($movedFile);

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
