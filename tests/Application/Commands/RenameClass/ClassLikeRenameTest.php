<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class ClassLikeRenameTest extends ElephactorTestCase
{
    private VirtualFile $interfaceFile;
    private VirtualFile $traitFile;
    private VirtualFile $enumFile;

    public function setUp(): void
    {
        parent::setUp();

        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $this->interfaceFile = $contractsDir->createFile('LegacyInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface LegacyInterface
        {
        }
        PHP);

        $behaviorDir = $this->sourceDirectory->createOrGetDirecotry('Behavior');
        $this->traitFile = $behaviorDir->createFile('LegacyTrait.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Behavior;

        trait LegacyTrait
        {
            public function flag(): bool
            {
                return true;
            }
        }
        PHP);

        $stateDir = $this->sourceDirectory->createOrGetDirecotry('State');
        $this->enumFile = $stateDir->createFile('LegacyStatus.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\State;

        enum LegacyStatus
        {
            case OPEN;
            case CLOSED;
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesInterfaceDefinition(): void
    {
        $this->renameClassLike('LegacyInterface', 'RenamedInterface');

        self::assertEquals('RenamedInterface.php', $this->interfaceFile->name());
        self::codeMatches($this->interfaceFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface RenamedInterface
        {
        }
        PHP);
    }

    public function testRenamesTraitDefinition(): void
    {
        $this->renameClassLike('LegacyTrait', 'RenamedTrait');

        self::assertEquals('RenamedTrait.php', $this->traitFile->name());
        self::codeMatches($this->traitFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Behavior;

        trait RenamedTrait
        {
            public function flag(): bool
            {
                return true;
            }
        }
        PHP);
    }

    public function testRenamesEnumDefinition(): void
    {
        $this->renameClassLike('LegacyStatus', 'RenamedStatus');

        self::assertEquals('RenamedStatus.php', $this->enumFile->name());
        self::codeMatches($this->enumFile->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\State;

        enum RenamedStatus
        {
            case OPEN;
            case CLOSED;
        }
        PHP);
    }

    private function renameClassLike(string $oldName, string $newName): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier($oldName)))->first();
        if ($class === null) {
            self::fail(sprintf('Class like %s not found in workspace', $oldName));
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier($newName)));
    }
}
