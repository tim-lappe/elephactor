<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class FullyQualifiedSelectionRenameTest extends ElephactorTestCase
{
    private FileHandle $primaryDuplicate;
    private FileHandle $secondaryDuplicate;
    private FileHandle $usage;

    protected function setUp(): void
    {
        $this->primaryDuplicate = $this->setupFile(['Utility', 'Primary'], 'DuplicateClass', <<<'PHP'
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

        $this->secondaryDuplicate = $this->setupFile(['Utility', 'Secondary'], 'DuplicateClass', <<<'PHP'
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

        $this->usage = $this->setupFile(['Consumer'], 'DuplicateClassConsumer', <<<'PHP'
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
    }

    public function testRenamesOnlySpecifiedFullyQualifiedClass(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('VirtualTestNamespace\Utility\Primary\DuplicateClass');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $application->getRefactoringExecutor()->handle(new ClassRename($class, new Identifier('PrimaryUtility')));

        $this->codeMatches($this->primaryDuplicate->readContent(), <<<'PHP'
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

        $this->codeMatches($this->secondaryDuplicate->readContent(), <<<'PHP'
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

        $this->codeMatches($this->usage->readContent(), <<<'PHP'
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

