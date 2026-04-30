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

final class ClassLikeMovingTest extends ElephactorTestCase
{
    public function testMovesInterfaceFileAndNamespace(): void
    {
        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $interfaceFile = $contractsDir->createFile('MovableInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface MovableInterface
        {
            public function toggle(): void;
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('NewArea')
            ->createOrGetDirecotry('Contracts');

        $this->workspace->reloadIndices();

        $this->moveClass('MovableInterface', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'MovableInterface.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\NewArea\Contracts;

        interface MovableInterface
        {
            public function toggle(): void;
        }
        PHP);
    }

    public function testMovesTraitFileAndNamespace(): void
    {
        $behaviorDir = $this->sourceDirectory->createOrGetDirecotry('Behavior');
        $behaviorDir->createFile('MovableTrait.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Behavior;

        trait MovableTrait
        {
            public function toggle(): void
            {
            }
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('NewArea')
            ->createOrGetDirecotry('Traits');

        $this->workspace->reloadIndices();

        $this->moveClass('MovableTrait', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'MovableTrait.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\NewArea\Traits;

        trait MovableTrait
        {
            public function toggle(): void
            {
            }
        }
        PHP);
    }

    public function testMovesEnumFileAndNamespace(): void
    {
        $stateDir = $this->sourceDirectory->createOrGetDirecotry('State');
        $stateDir->createFile('MovableStatus.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\State;

        enum MovableStatus
        {
            case STARTED;
            case FINISHED;
        }
        PHP);

        $targetDirectory = $this->sourceDirectory
            ->createOrGetDirecotry('NewArea')
            ->createOrGetDirecotry('StateMachine');

        $this->workspace->reloadIndices();

        $this->moveClass('MovableStatus', $targetDirectory);

        $movedFile = $this->findFileIn($targetDirectory, 'MovableStatus.php');
        self::assertNotNull($movedFile);

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\NewArea\StateMachine;

        enum MovableStatus
        {
            case STARTED;
            case FINISHED;
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
