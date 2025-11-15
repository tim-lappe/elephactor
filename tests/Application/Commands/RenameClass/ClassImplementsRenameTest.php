<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class ClassImplementsRenameTest extends ElephactorTestCase
{
    private FileHandle $targetInterface;
    private FileHandle $simpleImplementation;
    private FileHandle $complexImplementation;

    protected function setUp(): void
    {
        $this->targetInterface = $this->setupFile(['Contracts'], 'OldContract', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface OldContract
        {
            public function run(): void;
        }
        PHP);

        $this->setupFile(['Contracts'], 'AdditionalInterface', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface AdditionalInterface
        {
        }
        PHP);

        $this->simpleImplementation = $this->setupFile(['Usage'], 'SimpleImplementation', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Contracts\OldContract;

        class SimpleImplementation implements OldContract
        {
            public function run(): void
            {
            }
        }
        PHP);

        $this->complexImplementation = $this->setupFile(['Usage', 'Complex'], 'ComplexImplementation', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Complex;

        use VirtualTestNamespace\Contracts\AdditionalInterface;

        class ComplexImplementation implements \VirtualTestNamespace\Contracts\OldContract, AdditionalInterface
        {
            public function run(): void
            {
            }
        }
        PHP);
    }

    public function testRenamesImplementsClauseWithImport(): void
    {
        $this->renameContract();

        $this->codeMatches($this->simpleImplementation->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Contracts\NewContract;

        class SimpleImplementation implements NewContract
        {
            public function run(): void
            {
            }
        }
        PHP);
    }

    public function testRenamesFullyQualifiedImplementsClause(): void
    {
        $this->renameContract();

        $this->codeMatches($this->complexImplementation->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Complex;

        use VirtualTestNamespace\Contracts\AdditionalInterface;

        class ComplexImplementation implements \VirtualTestNamespace\Contracts\NewContract, AdditionalInterface
        {
            public function run(): void
            {
            }
        }
        PHP);
    }

    private function renameContract(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldContract');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewContract')));
    }
}

