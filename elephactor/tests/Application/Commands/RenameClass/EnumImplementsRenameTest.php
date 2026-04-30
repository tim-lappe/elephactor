<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class EnumImplementsRenameTest extends ElephactorTestCase
{
    private VirtualFile $simpleEnum;
    private VirtualFile $advancedEnum;

    public function setUp(): void
    {
        parent::setUp();

        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $contractsDir->createFile('BehaviorContract.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface BehaviorContract
        {
            public function describe(): string;
        }
        PHP);

        $this->sourceDirectory->createOrGetDirecotry('Contracts')->createFile('ExtraBehavior.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface ExtraBehavior
        {
        }
        PHP);

        $enumsDir = $this->sourceDirectory->createOrGetDirecotry('Enums');
        $this->simpleEnum = $enumsDir->createFile('SimpleEnum.php', <<<'PHP'
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

        $advancedDir = $enumsDir->createOrGetDirecotry('Advanced');
        $this->advancedEnum = $advancedDir->createFile('AdvancedEnum.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesEnumImplementsClause(): void
    {
        $this->renameContract();

        $this->codeMatches($this->simpleEnum->content(), <<<'PHP'
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

        $this->codeMatches($this->advancedEnum->content(), <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('BehaviorContract')))
            ->first();
        if ($class === null) {
            self::fail('Class BehaviorContract not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('UpdatedBehaviorContract')));
    }
}
