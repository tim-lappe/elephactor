<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

class BasicClassRenameTest extends ElephactorTestCase
{
    private FileHandle $oldClass;

    protected function setUp(): void
    {
        $this->oldClass = $this->setupFile([], 'OldClass', <<<'PHP'
        <?php

        class OldClass
        {
        }
        PHP);
    }

    public function testBasicClassRename(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldClass');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewClass')));

        $this->assertEquals('NewClass.php', $this->oldClass->name());
        $this->codeMatches($this->oldClass->readContent(), <<<'PHP'
        <?php

        class NewClass
        {
        }
        PHP);
    }

    public function setupFiles(): array
    {
        return [
            $this->oldClass
        ];
    }
}