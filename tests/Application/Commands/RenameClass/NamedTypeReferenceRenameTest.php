<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class NamedTypeReferenceRenameTest extends ElephactorTestCase
{
    private FileHandle $dependencyClass;
    private FileHandle $simpleTypeUsage;
    private FileHandle $qualifiedTypeUsage;

    protected function setUp(): void
    {
        $this->dependencyClass = $this->setupFile(['Types'], 'OldDependency', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Types;

        final class OldDependency
        {
        }
        PHP);

        $this->setupFile(['Types'], 'AuxDependency', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Types;

        final class AuxDependency
        {
        }
        PHP);

        $this->simpleTypeUsage = $this->setupFile(['Usage'], 'TypeHintedService', <<<'PHP'
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

        $this->qualifiedTypeUsage = $this->setupFile(['Usage', 'Qualified'], 'QualifiedTypeHintedService', <<<'PHP'
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
    }

    public function testRenamesNamedTypeHintsWithImports(): void
    {
        $this->renameDependency();

        $this->codeMatches($this->simpleTypeUsage->readContent(), <<<'PHP'
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

        $this->codeMatches($this->qualifiedTypeUsage->readContent(), <<<'PHP'
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
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldDependency');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewDependency')));
    }
}

