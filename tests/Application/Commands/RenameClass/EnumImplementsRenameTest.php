<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class EnumImplementsRenameTest extends ElephactorTestCase
{
    private FileHandle $contract;
    private FileHandle $simpleEnum;
    private FileHandle $advancedEnum;

    protected function setUp(): void
    {
        $this->contract = $this->setupFile(['Contracts'], 'BehaviorContract', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface BehaviorContract
        {
            public function describe(): string;
        }
        PHP);

        $this->setupFile(['Contracts'], 'ExtraBehavior', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface ExtraBehavior
        {
        }
        PHP);

        $this->simpleEnum = $this->setupFile(['Enums'], 'SimpleEnum', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Enums;

        use VirtualTestNamespace\Contracts\BehaviorContract;

        enum SimpleEnum implements BehaviorContract
        {
            case FIRST;

            public function describe(): string
            {
                return match ($this) {
                    self::FIRST => 'first',
                };
            }
        }
        PHP);

        $this->advancedEnum = $this->setupFile(['Enums', 'Advanced'], 'AdvancedEnum', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Enums\Advanced;

        use VirtualTestNamespace\Contracts\ExtraBehavior;

        enum AdvancedEnum implements \VirtualTestNamespace\Contracts\BehaviorContract, ExtraBehavior
        {
            case VALUE;

            public function describe(): string
            {
                return 'advanced';
            }
        }
        PHP);
    }

    public function testRenamesEnumImplementsClause(): void
    {
        $this->renameContract();

        $this->codeMatches($this->simpleEnum->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Enums;

        use VirtualTestNamespace\Contracts\UpdatedBehaviorContract;

        enum SimpleEnum implements UpdatedBehaviorContract
        {
            case FIRST;

            public function describe(): string
            {
                return match ($this) {
                    self::FIRST => 'first',
                };
            }
        }
        PHP);
    }

    public function testRenamesFullyQualifiedEnumImplementsClause(): void
    {
        $this->renameContract();

        $this->codeMatches($this->advancedEnum->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Enums\Advanced;

        use VirtualTestNamespace\Contracts\ExtraBehavior;

        enum AdvancedEnum implements \VirtualTestNamespace\Contracts\UpdatedBehaviorContract, ExtraBehavior
        {
            case VALUE;

            public function describe(): string
            {
                return 'advanced';
            }
        }
        PHP);
    }

    private function renameContract(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('BehaviorContract');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('UpdatedBehaviorContract')));
    }
}

