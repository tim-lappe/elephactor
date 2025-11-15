<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class InterfaceExtendsRenameTest extends ElephactorTestCase
{
    private FileHandle $baseInterface;
    private FileHandle $childInterface;
    private FileHandle $multiInterface;

    protected function setUp(): void
    {
        $this->baseInterface = $this->setupFile(['Contracts'], 'BaseInterface', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface BaseInterface
        {
            public function baseMethod(): void;
        }
        PHP);

        $this->setupFile(['Contracts'], 'StandaloneInterface', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface StandaloneInterface
        {
        }
        PHP);

        $this->childInterface = $this->setupFile(['Usage'], 'ChildInterface', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Contracts\BaseInterface;

        interface ChildInterface extends BaseInterface
        {
        }
        PHP);

        $this->multiInterface = $this->setupFile(['Usage', 'Complex'], 'MultiInterface', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Complex;

        use VirtualTestNamespace\Contracts\StandaloneInterface;

        interface MultiInterface extends \VirtualTestNamespace\Contracts\BaseInterface, StandaloneInterface
        {
        }
        PHP);
    }

    public function testRenamesInterfaceExtendsClause(): void
    {
        $this->renameBaseInterface();

        $this->codeMatches($this->childInterface->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Contracts\RenamedBaseInterface;

        interface ChildInterface extends RenamedBaseInterface
        {
        }
        PHP);
    }

    public function testRenamesInterfaceMultipleExtendsClause(): void
    {
        $this->renameBaseInterface();

        $this->codeMatches($this->multiInterface->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Complex;

        use VirtualTestNamespace\Contracts\StandaloneInterface;

        interface MultiInterface extends \VirtualTestNamespace\Contracts\RenamedBaseInterface, StandaloneInterface
        {
        }
        PHP);
    }

    private function renameBaseInterface(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('BaseInterface');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('RenamedBaseInterface')));
    }
}

