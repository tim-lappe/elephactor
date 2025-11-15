<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class TraitUseRenameTest extends ElephactorTestCase
{
    private FileHandle $traitFile;
    private FileHandle $simpleUsage;
    private FileHandle $qualifiedUsage;

    protected function setUp(): void
    {
        $this->traitFile = $this->setupFile(['Traits'], 'OldTrait', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Traits;

        trait OldTrait
        {
            public function log(): string
            {
                return 'running';
            }
        }
        PHP);

        $this->simpleUsage = $this->setupFile(['Usage'], 'SimpleTraitConsumer', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Traits\OldTrait;

        class SimpleTraitConsumer
        {
            use OldTrait;
        }
        PHP);

        $this->qualifiedUsage = $this->setupFile(['Usage', 'Qualified'], 'QualifiedTraitConsumer', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        class QualifiedTraitConsumer
        {
            use \VirtualTestNamespace\Traits\OldTrait;
        }
        PHP);
    }

    public function testRenamesTraitUseWithImport(): void
    {
        $this->renameTrait();

        $this->codeMatches($this->simpleUsage->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Traits\NewTrait;

        class SimpleTraitConsumer
        {
            use NewTrait;
        }
        PHP);
    }

    public function testRenamesFullyQualifiedTraitUse(): void
    {
        $this->renameTrait();

        $this->codeMatches($this->qualifiedUsage->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        class QualifiedTraitConsumer
        {
            use \VirtualTestNamespace\Traits\NewTrait;
        }
        PHP);
    }

    private function renameTrait(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldTrait');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewTrait')));
    }
}

