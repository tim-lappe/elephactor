<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class NewExpressionRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleFactory;
    private VirtualFile $qualifiedFactory;

    public function setUp(): void
    {
        parent::setUp();

        $servicesDir = $this->sourceDirectory->createOrGetDirecotry('Services');
        $servicesDir->createFile('OldService.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Services;

        class OldService
        {
        }
        PHP);

        $factoriesDir = $this->sourceDirectory->createOrGetDirecotry('Factories');
        $this->simpleFactory = $factoriesDir->createFile('SimpleFactory.php', <<<'PHP'
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

        $advancedDir = $factoriesDir->createOrGetDirecotry('Advanced');
        $this->qualifiedFactory = $advancedDir->createFile('QualifiedFactory.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesNewExpressionWithImport(): void
    {
        $this->renameService();

        $this->codeMatches($this->simpleFactory->content(), <<<'PHP'
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

        $this->codeMatches($this->qualifiedFactory->content(), <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('OldService')))
            ->first();
        if ($class === null) {
            self::fail('Class OldService not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewService')));
    }
}
