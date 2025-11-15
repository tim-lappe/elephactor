<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class UseStatementRenameTest extends ElephactorTestCase
{
    private FileHandle $targetClass;
    private FileHandle $simpleImportClass;
    private FileHandle $groupedImportClass;

    protected function setUp(): void
    {
        $this->targetClass = $this->setupFile(['Foo'], 'OldClass', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Foo;

        class OldClass
        {
        }
        PHP);

        $this->setupFile(['Foo'], 'HelperClass', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Foo;

        class HelperClass
        {
        }
        PHP);

        $this->simpleImportClass = $this->setupFile(['Bar'], 'SimpleImportClass', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Bar;

        use VirtualTestNamespace\Foo\OldClass;

        class SimpleImportClass
        {
            public function reference(): string
            {
                return OldClass::class;
            }
        }
        PHP);

        $this->groupedImportClass = $this->setupFile(['Bar', 'Grouped'], 'GroupedImportClass', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Bar\Grouped;

        use VirtualTestNamespace\Foo\{OldClass, HelperClass};

        class GroupedImportClass
        {
            public function createInstance(): string
            {
                return OldClass::class;
            }
        }
        PHP);
    }

    public function testRenamesSimpleUseStatement(): void
    {
        $this->renameTargetClass();

        $this->codeMatches($this->simpleImportClass->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Bar;

        use VirtualTestNamespace\Foo\NewClass;

        class SimpleImportClass
        {
            public function reference(): string
            {
                return NewClass::class;
            }
        }
        PHP);
    }

    public function testRenamesGroupedUseStatement(): void
    {
        $this->renameTargetClass();

        $this->codeMatches($this->groupedImportClass->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Bar\Grouped;

        use VirtualTestNamespace\Foo\{NewClass, HelperClass};

        class GroupedImportClass
        {
            public function createInstance(): string
            {
                return NewClass::class;
            }
        }
        PHP);
    }

    private function renameTargetClass(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldClass');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewClass')));
    }
}

