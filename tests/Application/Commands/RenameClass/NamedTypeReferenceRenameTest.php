<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class NamedTypeReferenceRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleTypeUsage;
    private VirtualFile $qualifiedTypeUsage;

    public function setUp(): void
    {
        parent::setUp();

        $typesDir = $this->sourceDirectory->createOrGetDirecotry('Types');
        $typesDir->createFile('OldDependency.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Types;

        final class OldDependency
        {
        }
        PHP);

        $typesDir->createFile('AuxDependency.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Types;

        final class AuxDependency
        {
        }
        PHP);

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->simpleTypeUsage = $usageDir->createFile('TypeHintedService.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Types\OldDependency;

        final class TypeHintedService
        {
            private OldDependency $dependency;

            public function __construct(OldDependency $dependency)
            {
                $this->dependency = $dependency;
            }

            public function replace(OldDependency $dependency): OldDependency
            {
                $this->dependency = $dependency;
                return $this->dependency;
            }
        }
        PHP);

        $qualifiedDir = $usageDir->createOrGetDirecotry('Qualified');
        $this->qualifiedTypeUsage = $qualifiedDir->createFile('QualifiedTypeHintedService.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        final class QualifiedTypeHintedService
        {
            private \VirtualTestNamespace\Types\OldDependency $dependency;

            public function transform(\VirtualTestNamespace\Types\OldDependency $dependency): \VirtualTestNamespace\Types\OldDependency
            {
                $this->dependency = $dependency;
                return $this->dependency;
            }
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesNamedTypeHintsWithImports(): void
    {
        $this->renameDependency();

        $this->codeMatches($this->simpleTypeUsage->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Types\NewDependency;

        final class TypeHintedService
        {
            private NewDependency $dependency;

            public function __construct(NewDependency $dependency)
            {
                $this->dependency = $dependency;
            }

            public function replace(NewDependency $dependency): NewDependency
            {
                $this->dependency = $dependency;
                return $this->dependency;
            }
        }
        PHP);
    }

    public function testRenamesFullyQualifiedNamedTypes(): void
    {
        $this->renameDependency();

        $this->codeMatches($this->qualifiedTypeUsage->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        final class QualifiedTypeHintedService
        {
            private \VirtualTestNamespace\Types\NewDependency $dependency;

            public function transform(\VirtualTestNamespace\Types\NewDependency $dependency): \VirtualTestNamespace\Types\NewDependency
            {
                $this->dependency = $dependency;
                return $this->dependency;
            }
        }
        PHP);
    }

    private function renameDependency(): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('OldDependency')))
            ->first();
        if ($class === null) {
            self::fail('Class OldDependency not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewDependency')));
    }
}
