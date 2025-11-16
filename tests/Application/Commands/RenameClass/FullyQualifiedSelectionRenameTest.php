<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class FullyQualifiedSelectionRenameTest extends ElephactorTestCase
{
    private VirtualFile $primaryDuplicate;
    private VirtualFile $secondaryDuplicate;
    private VirtualFile $usage;

    public function setUp(): void
    {
        parent::setUp();

        $utilityDir = $this->sourceDirectory->createOrGetDirecotry('Utility');
        $primaryDir = $utilityDir->createOrGetDirecotry('Primary');
        $this->primaryDuplicate = $primaryDir->createFile('DuplicateClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Utility\Primary;

        class DuplicateClass
        {
            public function describe(): string
            {
                return 'primary';
            }
        }
        PHP);

        $secondaryDir = $utilityDir->createOrGetDirecotry('Secondary');
        $this->secondaryDuplicate = $secondaryDir->createFile('DuplicateClass.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Utility\Secondary;

        class DuplicateClass
        {
            public function describe(): string
            {
                return 'secondary';
            }
        }
        PHP);

        $consumerDir = $this->sourceDirectory->createOrGetDirecotry('Consumer');
        $this->usage = $consumerDir->createFile('DuplicateClassConsumer.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumer;

        use VirtualTestNamespace\Utility\Primary\DuplicateClass;

        final class DuplicateClassConsumer
        {
            public function build(): DuplicateClass
            {
                return new DuplicateClass();
            }
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesOnlySpecifiedFullyQualifiedClass(): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(QualifiedName::fromString('VirtualTestNamespace\Utility\Primary\DuplicateClass')))
            ->first();

        if ($class === null) {
            self::fail('Class VirtualTestNamespace\Utility\Primary\DuplicateClass not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('PrimaryUtility')));

        $this->codeMatches($this->primaryDuplicate->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Utility\Primary;

        class PrimaryUtility
        {
            public function describe(): string
            {
                return 'primary';
            }
        }
        PHP);

        $this->codeMatches($this->secondaryDuplicate->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Utility\Secondary;

        class DuplicateClass
        {
            public function describe(): string
            {
                return 'secondary';
            }
        }
        PHP);

        $this->codeMatches($this->usage->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Consumer;

        use VirtualTestNamespace\Utility\Primary\PrimaryUtility;

        final class DuplicateClassConsumer
        {
            public function build(): PrimaryUtility
            {
                return new PrimaryUtility();
            }
        }
        PHP);
    }
}
