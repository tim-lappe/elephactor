<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\MoveClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Psr4\Refactoring\Commands\MoveClassFile;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualDirectory;
use TimLappe\ElephactorTests\Application\VirtualFile;

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
        $this->assertNotNull($movedFile);
        if ($movedFile === null) {
            return;
        }

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
        $this->assertNotNull($movedFile);
        if ($movedFile === null) {
            return;
        }

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
        $this->assertNotNull($movedFile);
        if ($movedFile === null) {
            return;
        }

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
