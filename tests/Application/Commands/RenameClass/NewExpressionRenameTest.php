<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class NewExpressionRenameTest extends ElephactorTestCase
{
    private FileHandle $serviceClass;
    private FileHandle $simpleFactory;
    private FileHandle $qualifiedFactory;

    protected function setUp(): void
    {
        $this->serviceClass = $this->setupFile(['Services'], 'OldService', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        class OldService
        {
        }
        PHP);

        $this->simpleFactory = $this->setupFile(['Factories'], 'SimpleFactory', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Factories;

        use VirtualTestNamespace\Services\OldService;

        class SimpleFactory
        {
            public function build(): object
            {
                return new OldService();
            }
        }
        PHP);

        $this->qualifiedFactory = $this->setupFile(['Factories', 'Advanced'], 'QualifiedFactory', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Factories\Advanced;

        class QualifiedFactory
        {
            public function build(): object
            {
                return new \VirtualTestNamespace\Services\OldService();
            }
        }
        PHP);
    }

    public function testRenamesNewExpressionWithImport(): void
    {
        $this->renameService();

        $this->codeMatches($this->simpleFactory->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Factories;

        use VirtualTestNamespace\Services\NewService;

        class SimpleFactory
        {
            public function build(): object
            {
                return new NewService();
            }
        }
        PHP);
    }

    public function testRenamesFullyQualifiedNewExpression(): void
    {
        $this->renameService();

        $this->codeMatches($this->qualifiedFactory->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Factories\Advanced;

        class QualifiedFactory
        {
            public function build(): object
            {
                return new \VirtualTestNamespace\Services\NewService();
            }
        }
        PHP);
    }

    private function renameService(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldService');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewService')));
    }
}

