<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class StaticReferenceRenameTest extends ElephactorTestCase
{
    private FileHandle $utilityClass;
    private FileHandle $methodUsage;
    private FileHandle $memberUsage;

    protected function setUp(): void
    {
        $this->utilityClass = $this->setupFile(['Utility'], 'OldUtility', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Utility;

        class OldUtility
        {
            public const VERSION = '1.0';
            public static string $state = 'idle';

            public static function perform(): string
            {
                return 'performing';
            }
        }
        PHP);

        $this->methodUsage = $this->setupFile(['Usage'], 'StaticCallUsage', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Utility\OldUtility;

        class StaticCallUsage
        {
            public function call(): string
            {
                return OldUtility::perform();
            }
        }
        PHP);

        $this->memberUsage = $this->setupFile(['Usage', 'Members'], 'StaticMembersUsage', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Members;

        class StaticMembersUsage
        {
            public function info(): string
            {
                return \VirtualTestNamespace\Utility\OldUtility::$state . \VirtualTestNamespace\Utility\OldUtility::VERSION;
            }
        }
        PHP);
    }

    public function testRenamesStaticMethodCalls(): void
    {
        $this->renameUtility();

        $this->codeMatches($this->methodUsage->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Utility\NewUtility;

        class StaticCallUsage
        {
            public function call(): string
            {
                return NewUtility::perform();
            }
        }
        PHP);
    }

    public function testRenamesStaticMembersAndConstants(): void
    {
        $this->renameUtility();

        $this->codeMatches($this->memberUsage->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Members;

        class StaticMembersUsage
        {
            public function info(): string
            {
                return \VirtualTestNamespace\Utility\NewUtility::$state . \VirtualTestNamespace\Utility\NewUtility::VERSION;
            }
        }
        PHP);
    }

    private function renameUtility(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldUtility');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewUtility')));
    }
}

