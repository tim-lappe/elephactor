<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class TraitAdaptationRenameTest extends ElephactorTestCase
{
    private FileHandle $legacyTrait;
    private FileHandle $aliasConsumer;
    private FileHandle $precedenceConsumer;

    protected function setUp(): void
    {
        $this->legacyTrait = $this->setupFile(['Traits'], 'LegacyTrait', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Traits;

        trait LegacyTrait
        {
            public function run(): string
            {
                return 'legacy';
            }

            public function conflict(): string
            {
                return 'legacy-conflict';
            }
        }
        PHP);

        $this->setupFile(['Traits'], 'SecondaryTrait', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Traits;

        trait SecondaryTrait
        {
            public function conflict(): string
            {
                return 'secondary';
            }
        }
        PHP);

        $this->aliasConsumer = $this->setupFile(['Usage'], 'AliasTraitConsumer', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Traits\LegacyTrait;

        class AliasTraitConsumer
        {
            use LegacyTrait {
                LegacyTrait::run as runAlias;
            }
        }
        PHP);

        $this->precedenceConsumer = $this->setupFile(['Usage', 'Precedence'], 'PrecedenceTraitConsumer', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Precedence;

        use VirtualTestNamespace\Traits\LegacyTrait;
        use VirtualTestNamespace\Traits\SecondaryTrait;

        class PrecedenceTraitConsumer
        {
            use LegacyTrait, SecondaryTrait {
                LegacyTrait::conflict insteadof SecondaryTrait;
                SecondaryTrait::conflict insteadof LegacyTrait;
            }
        }
        PHP);
    }

    public function testRenamesTraitAliasAdaptation(): void
    {
        $this->renameTrait();

        $this->codeMatches($this->aliasConsumer->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Traits\ModernTrait;

        class AliasTraitConsumer
        {
            use ModernTrait {
                ModernTrait::run as runAlias;
            }
        }
        PHP);
    }

    public function testRenamesTraitPrecedenceAdaptation(): void
    {
        $this->renameTrait();

        $this->codeMatches($this->precedenceConsumer->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Precedence;

        use VirtualTestNamespace\Traits\ModernTrait;
        use VirtualTestNamespace\Traits\SecondaryTrait;

        class PrecedenceTraitConsumer
        {
            use ModernTrait, SecondaryTrait {
                ModernTrait::conflict insteadof SecondaryTrait;
                SecondaryTrait::conflict insteadof ModernTrait;
            }
        }
        PHP);
    }

    private function renameTrait(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('LegacyTrait');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('ModernTrait')));
    }
}

