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

final class MoveClassDependencyImportTest extends ElephactorTestCase
{
    public function testKeepsReferencesToSameNamespaceDependencies(): void
    {
        $sharedNamespace = $this->sourceDirectory
            ->createOrGetDirecotry('Domain')
            ->createOrGetDirecotry('Shared');

        $sharedNamespace->createFile('DependencyClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Domain\Shared;

        class DependencyClass
        {
        }
        PHP);

        $classFile = $sharedNamespace->createFile('MovedClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Domain\Shared;

        class MovedClass
        {
            public function __construct(private DependencyClass $dependency)
            {
            }

            public function dependency(): DependencyClass
            {
                return $this->dependency;
            }
        }
        PHP);

        $targetNamespace = $this->sourceDirectory
            ->createOrGetDirecotry('Domain')
            ->createOrGetDirecotry('Target');

        $this->workspace->reloadIndices();

        $this->moveClass('MovedClass', $targetNamespace);

        $movedFile = $this->findFileIn($targetNamespace, 'MovedClass.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Domain\Target;

        class MovedClass
        {
            public function __construct(private \VirtualTestNamespace\Domain\Shared\DependencyClass $dependency)
            {
            }

            public function dependency(): \VirtualTestNamespace\Domain\Shared\DependencyClass
            {
                return $this->dependency;
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
