<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class InstanceofExpressionRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleChecker;
    private VirtualFile $qualifiedChecker;

    public function setUp(): void
    {
        parent::setUp();

        $typesDir = $this->sourceDirectory->createOrGetDirecotry('Types');
        $typesDir->createFile('OldType.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Types;

        class OldType
        {
        }
        PHP);

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->simpleChecker = $usageDir->createFile('InstanceofChecker.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Types\OldType;

        class InstanceofChecker
        {
            public function matches(object $value): bool
            {
                return $value instanceof OldType;
            }
        }
        PHP);

        $advancedDir = $usageDir->createOrGetDirecotry('Advanced');
        $this->qualifiedChecker = $advancedDir->createFile('QualifiedInstanceofChecker.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Advanced;

        class QualifiedInstanceofChecker
        {
            public function matches(object $value): bool
            {
                return $value instanceof \VirtualTestNamespace\Types\OldType;
            }
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesInstanceofWithImport(): void
    {
        $this->renameType();

        $this->codeMatches($this->simpleChecker->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Types\NewType;

        class InstanceofChecker
        {
            public function matches(object $value): bool
            {
                return $value instanceof NewType;
            }
        }
        PHP);
    }

    public function testRenamesFullyQualifiedInstanceof(): void
    {
        $this->renameType();

        $this->codeMatches($this->qualifiedChecker->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Advanced;

        class QualifiedInstanceofChecker
        {
            public function matches(object $value): bool
            {
                return $value instanceof \VirtualTestNamespace\Types\NewType;
            }
        }
        PHP);
    }

    private function renameType(): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('OldType')))
            ->first();
        if ($class === null) {
            self::fail('Class OldType not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewType')));
    }
}
