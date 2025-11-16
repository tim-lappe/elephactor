<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class TraitUseRenameTest extends ElephactorTestCase
{
    private VirtualFile $traitFile;
    private VirtualFile $simpleUsage;
    private VirtualFile $qualifiedUsage;

    public function setUp(): void
    {
        parent::setUp();

        $traitsDir = $this->sourceDirectory->createOrGetDirecotry('Traits');
        $this->traitFile = $traitsDir->createFile('OldTrait.php', <<<'PHP'
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

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->simpleUsage = $usageDir->createFile('SimpleTraitConsumer.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Traits\OldTrait;

        class SimpleTraitConsumer
        {
            use OldTrait;
        }
        PHP);

        $qualifiedDir = $usageDir->createOrGetDirecotry('Qualified');
        $this->qualifiedUsage = $qualifiedDir->createFile('QualifiedTraitConsumer.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        class QualifiedTraitConsumer
        {
            use \VirtualTestNamespace\Traits\OldTrait;
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesTraitUseWithImport(): void
    {
        $this->renameTrait();

        $this->codeMatches($this->simpleUsage->content(), <<<'PHP'
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

        $this->codeMatches($this->qualifiedUsage->content(), <<<'PHP'
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
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria('OldTrait'));
        if ($class->first() === null) {
            $this->fail('Class OldTrait not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class->first(), new Identifier('NewTrait')));
    }
}

