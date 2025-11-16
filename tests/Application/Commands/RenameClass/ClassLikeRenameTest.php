<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class ClassLikeRenameTest extends ElephactorTestCase
{
    private VirtualFile $interfaceHandle;
    private VirtualFile $traitHandle;
    private VirtualFile $enumHandle;

    public function setUp(): void
    {
        parent::setUp();

        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $this->interfaceHandle = $contractsDir->createFile('LegacyInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface LegacyInterface
        {
        }
        PHP);

        $behaviorDir = $this->sourceDirectory->createOrGetDirecotry('Behavior');
        $this->traitHandle = $behaviorDir->createFile('LegacyTrait.php', <<<'PHP'
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
        $this->enumHandle = $stateDir->createFile('LegacyStatus.php', <<<'PHP'
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

        $this->assertEquals('RenamedInterface.php', $this->interfaceHandle->name());
        $this->codeMatches($this->interfaceHandle->content(), <<<'PHP'
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

        $this->assertEquals('RenamedTrait.php', $this->traitHandle->name());
        $this->codeMatches($this->traitHandle->content(), <<<'PHP'
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

        $this->assertEquals('RenamedStatus.php', $this->enumHandle->name());
        $this->codeMatches($this->enumHandle->content(), <<<'PHP'
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
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria($oldName));
        if ($class->first() === null) {
            $this->fail(sprintf('Class %s not found in workspace', $oldName));
        }

        $this->application->refactoringExecutor()->handle(new ClassRename($class->first(), new Identifier($newName)));
    }
}
