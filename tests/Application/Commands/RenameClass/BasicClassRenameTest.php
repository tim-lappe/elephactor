<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
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

        self::assertEquals('NewClass.php', $this->oldClass->name());
        $this->codeMatches($this->oldClass->content(), <<<'PHP'
        <?php

        class NewClass
        {
        }
        PHP);
    }

    private function renameClass(string $oldName, string $newName): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier($oldName)))->first();
        if ($class === null) {
            self::fail(sprintf('Class %s not found in workspace', $oldName));
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier($newName)));
    }
}
