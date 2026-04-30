<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class UseStatementRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleImportClass;
    private VirtualFile $groupedImportClass;

    public function setUp(): void
    {
        parent::setUp();

        $fooDir = $this->sourceDirectory->createOrGetDirecotry('Foo');
        $fooDir->createFile('OldClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Foo;

        class OldClass
        {
        }
        PHP);

        $fooDir->createFile('HelperClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Foo;

        class HelperClass
        {
        }
        PHP);

        $barDir = $this->sourceDirectory->createOrGetDirecotry('Bar');
        $this->simpleImportClass = $barDir->createFile('SimpleImportClass.php', <<<'PHP'
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

        $groupedDir = $barDir->createOrGetDirecotry('Grouped');
        $this->groupedImportClass = $groupedDir->createFile('GroupedImportClass.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesSimpleUseStatement(): void
    {
        $this->renameTargetClass();

        $this->codeMatches($this->simpleImportClass->content(), <<<'PHP'
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

        $this->codeMatches($this->groupedImportClass->content(), <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('OldClass')))
            ->first();
        if ($class === null) {
            self::fail('Class OldClass not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewClass')));
    }
}
