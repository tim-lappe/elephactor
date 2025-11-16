<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class ClassExtendsRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleChild;
    private VirtualFile $qualifiedChild;

    public function setUp(): void
    {
        parent::setUp();

        $inheritanceDir = $this->sourceDirectory->createOrGetDirecotry('Inheritance');
        $inheritanceDir->createFile('ParentBase.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Inheritance;

        class ParentBase
        {
        }
        PHP);

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->simpleChild = $usageDir->createFile('SimpleChild.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Inheritance\ParentBase;

        class SimpleChild extends ParentBase
        {
        }
        PHP);

        $deepDir = $usageDir->createOrGetDirecotry('Deep');
        $this->qualifiedChild = $deepDir->createFile('QualifiedChild.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Deep;

        class QualifiedChild extends \VirtualTestNamespace\Inheritance\ParentBase
        {
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesExtendsClauseWithImport(): void
    {
        $this->renameParent();

        $this->codeMatches($this->simpleChild->content(), <<<'PHP'
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

        $this->codeMatches($this->qualifiedChild->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Deep;

        class QualifiedChild extends \VirtualTestNamespace\Inheritance\RenamedParentBase
        {
        }
        PHP);
    }

    private function renameParent(): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('ParentBase')))
            ->first();
        if ($class === null) {
            self::fail('Class ParentBase not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('RenamedParentBase')));
    }
}
