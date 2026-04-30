<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class ClassImplementsRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleImplementation;
    private VirtualFile $complexImplementation;

    public function setUp(): void
    {
        parent::setUp();

        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $contractsDir->createFile('OldContract.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface OldContract
        {
            public function run(): void;
        }
        PHP);

        $contractsDir->createFile('AdditionalInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface AdditionalInterface
        {
        }
        PHP);

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->simpleImplementation = $usageDir->createFile('SimpleImplementation.php', <<<'PHP'
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

        $complexDir = $usageDir->createOrGetDirecotry('Complex');
        $this->complexImplementation = $complexDir->createFile('ComplexImplementation.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesImplementsClauseWithImport(): void
    {
        $this->renameContract();

        $this->codeMatches($this->simpleImplementation->content(), <<<'PHP'
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

        $this->codeMatches($this->complexImplementation->content(), <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('OldContract')))
            ->first();
        if ($class === null) {
            self::fail('Class OldContract not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewContract')));
    }
}
