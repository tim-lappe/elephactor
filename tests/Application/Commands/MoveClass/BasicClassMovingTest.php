<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\MoveClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Psr4\Refactoring\Commands\MoveClassFile;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualDirectory;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class BasicClassMovingTest extends ElephactorTestCase
{
    private VirtualFile $classFile;
    private VirtualDirectory $targetDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $sourceNamespace = $this->sourceDirectory->createOrGetDirecotry('TestNamespace');
        $this->classFile = $sourceNamespace->createFile('FooClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\TestNamespace;

        class FooClass
        {
        }
        PHP);

        $this->targetDirectory = $this->sourceDirectory->createOrGetDirecotry('NewDirectory');

        $this->workspace->reloadIndices();
    }

    public function testBasicClassMoving(): void
    {
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria('FooClass'));
        if ($class->first() === null) {
            $this->fail('Class FooClass not found in workspace');
        }

        $this->application
            ->refactoringExecutor()
            ->handle(new MoveClassFile($class->first(), $this->targetDirectory));

        $movedFile = $this->targetDirectory
            ->childFiles()
            ->first(fn (VirtualFile $file): bool => $file->name() === 'FooClass.php');

        $this->assertNotNull($movedFile, 'Moved file not found in target directory');

        if ($movedFile === null) {
            return;
        }

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\NewDirectory;

        class FooClass
        {
        }
        PHP);
    }
}