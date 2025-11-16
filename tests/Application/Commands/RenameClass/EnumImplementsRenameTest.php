<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class EnumImplementsRenameTest extends ElephactorTestCase
{
    private VirtualFile $contract;
    private VirtualFile $simpleEnum;
    private VirtualFile $advancedEnum;

    public function setUp(): void
    {
        parent::setUp();

        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $this->contract = $contractsDir->createFile('BehaviorContract.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface BehaviorContract
        {
            public function describe(): string;
        }
        PHP);

        $contractsDir->createFile('ExtraBehavior.php', <<<'PHP'
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
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria('BehaviorContract'));
        if ($class->first() === null) {
            $this->fail('Class BehaviorContract not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class->first(), new Identifier('UpdatedBehaviorContract')));
    }
}
