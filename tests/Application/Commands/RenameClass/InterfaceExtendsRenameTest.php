<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class InterfaceExtendsRenameTest extends ElephactorTestCase
{
    private VirtualFile $childInterface;
    private VirtualFile $multiInterface;

    public function setUp(): void
    {
        parent::setUp();

        $contractsDir = $this->sourceDirectory->createOrGetDirecotry('Contracts');
        $contractsDir->createFile('BaseInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface BaseInterface
        {
            public function baseMethod(): void;
        }
        PHP);

        $contractsDir->createFile('StandaloneInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Contracts;

        interface StandaloneInterface
        {
        }
        PHP);

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->childInterface = $usageDir->createFile('ChildInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Contracts\BaseInterface;

        interface ChildInterface extends BaseInterface
        {
        }
        PHP);

        $complexDir = $usageDir->createOrGetDirecotry('Complex');
        $this->multiInterface = $complexDir->createFile('MultiInterface.php', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Complex;

        use VirtualTestNamespace\Contracts\StandaloneInterface;

        interface MultiInterface extends \VirtualTestNamespace\Contracts\BaseInterface, StandaloneInterface
        {
        }
        PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesInterfaceExtendsClause(): void
    {
        $this->renameBaseInterface();

        $this->codeMatches($this->childInterface->content(), <<<'PHP'
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

        $this->codeMatches($this->multiInterface->content(), <<<'PHP'
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
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier('BaseInterface')))
            ->first();
        if ($class === null) {
            self::fail('Class BaseInterface not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('RenamedBaseInterface')));
    }
}
