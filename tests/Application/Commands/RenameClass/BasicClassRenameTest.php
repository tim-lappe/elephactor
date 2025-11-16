<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

class BasicClassRenameTest extends ElephactorTestCase
{
    private VirtualFile $oldClass;

    public function setUp(): void
    {
        parent::setUp();

        $this->oldClass = $this->sourceDirectory->createFile('OldClass.php', <<<'PHP'
        <?php

        class OldClass
        {
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testBasicClassRename(): void
    {
        $this->renameClass('OldClass', 'NewClass');

        $this->assertEquals('NewClass.php', $this->oldClass->name());
        $this->codeMatches($this->oldClass->content(), <<<'PHP'
        <?php

        class NewClass
        {
        }
        PHP);
    }

    private function renameClass(string $oldName, string $newName): void
    {
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria($oldName));
        if ($class->first() === null) {
            $this->fail(sprintf('Class %s not found in workspace', $oldName));
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class->first(), new Identifier($newName)));
    }
}