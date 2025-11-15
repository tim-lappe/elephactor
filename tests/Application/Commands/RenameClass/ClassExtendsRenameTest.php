<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class ClassExtendsRenameTest extends ElephactorTestCase
{
    private FileHandle $parentClass;
    private FileHandle $simpleChild;
    private FileHandle $qualifiedChild;

    protected function setUp(): void
    {
        $this->parentClass = $this->setupFile(['Inheritance'], 'ParentBase', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Inheritance;

        class ParentBase
        {
        }
        PHP);

        $this->simpleChild = $this->setupFile(['Usage'], 'SimpleChild', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Inheritance\ParentBase;

        class SimpleChild extends ParentBase
        {
        }
        PHP);

        $this->qualifiedChild = $this->setupFile(['Usage', 'Deep'], 'QualifiedChild', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Deep;

        class QualifiedChild extends \VirtualTestNamespace\Inheritance\ParentBase
        {
        }
        PHP);
    }

    public function testRenamesExtendsClauseWithImport(): void
    {
        $this->renameParent();

        $this->codeMatches($this->simpleChild->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Inheritance\RenamedParentBase;

        class SimpleChild extends RenamedParentBase
        {
        }
        PHP);
    }

    public function testRenamesFullyQualifiedExtendsClause(): void
    {
        $this->renameParent();

        $this->codeMatches($this->qualifiedChild->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Deep;

        class QualifiedChild extends \VirtualTestNamespace\Inheritance\RenamedParentBase
        {
        }
        PHP);
    }

    private function renameParent(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('ParentBase');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('RenamedParentBase')));
    }
}

