<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\MoveClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\MoveFile;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualDirectory;
use TimLappe\ElephactorTests\Application\VirtualFile;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Psr4\Model\Psr4ClassFile;

final class BasicClassMovingTest extends ElephactorTestCase
{
    private VirtualDirectory $targetDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $sourceNamespace = $this->sourceDirectory->createOrGetDirecotry('TestNamespace');
        $sourceNamespace->createFile('FooClass.php', <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('FooClass')))->first();
        if ($class === null) {
            self::fail('Class FooClass not found in workspace.');
        }

        if (!$class instanceof Psr4ClassFile) {
            self::fail('Class FooClass is not a Psr4ClassFile');
        }

        $this->application
            ->refactoringExecutor()
            ->handle(new MoveFile($class->file(), $this->targetDirectory));

        $movedFile = $this->targetDirectory
            ->childFiles()
            ->first(fn (VirtualFile $file): bool => $file->name() === 'FooClass.php');

        self::assertNotNull($movedFile, 'Moved file not found in target directory');

        $this->codeMatches($movedFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\NewDirectory;

        class FooClass
        {
        }
        PHP);
    }
}
