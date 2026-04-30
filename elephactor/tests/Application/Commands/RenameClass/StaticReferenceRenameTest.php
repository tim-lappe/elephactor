<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class StaticReferenceRenameTest extends ElephactorTestCase
{
    private VirtualFile $methodUsage;
    private VirtualFile $memberUsage;

    public function setUp(): void
    {
        parent::setUp();

        $utilityDir = $this->sourceDirectory->createOrGetDirecotry('Utility');
        $utilityDir->createFile('OldUtility.php', <<<'PHP'
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

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->methodUsage = $usageDir->createFile('StaticCallUsage.php', <<<'PHP'
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

        $membersDir = $usageDir->createOrGetDirecotry('Members');
        $this->memberUsage = $membersDir->createFile('StaticMembersUsage.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesStaticMethodCalls(): void
    {
        $this->renameUtility();

        $this->codeMatches($this->methodUsage->content(), <<<'PHP'
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

        $this->codeMatches($this->memberUsage->content(), <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('OldUtility')))
            ->first();
        if ($class === null) {
            self::fail('Class OldUtility not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewUtility')));
    }
}
