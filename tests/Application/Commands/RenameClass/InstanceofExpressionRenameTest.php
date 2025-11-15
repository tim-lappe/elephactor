<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class InstanceofExpressionRenameTest extends ElephactorTestCase
{
    private FileHandle $typeClass;
    private FileHandle $simpleChecker;
    private FileHandle $qualifiedChecker;

    protected function setUp(): void
    {
        $this->typeClass = $this->setupFile(['Types'], 'OldType', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Types;

        class OldType
        {
        }
        PHP);

        $this->simpleChecker = $this->setupFile(['Usage'], 'InstanceofChecker', <<<'PHP'
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

        $this->qualifiedChecker = $this->setupFile(['Usage', 'Advanced'], 'QualifiedInstanceofChecker', <<<'PHP'
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
    }

    public function testRenamesInstanceofWithImport(): void
    {
        $this->renameType();

        $this->codeMatches($this->simpleChecker->readContent(), <<<'PHP'
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

        $this->codeMatches($this->qualifiedChecker->readContent(), <<<'PHP'
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
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldType');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewType')));
    }
}

