<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class TraitAdaptationRenameTest extends ElephactorTestCase
{
    private VirtualFile $legacyTrait;
    private VirtualFile $aliasConsumer;
    private VirtualFile $precedenceConsumer;

    public function setUp(): void
    {
        parent::setUp();

        $traitsDir = $this->sourceDirectory->createOrGetDirecotry('Traits');
        $this->legacyTrait = $traitsDir->createFile('LegacyTrait.php', <<<'PHP'
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

        $traitsDir->createFile('SecondaryTrait.php', <<<'PHP'
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

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->aliasConsumer = $usageDir->createFile('AliasTraitConsumer.php', <<<'PHP'
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

        $precedenceDir = $usageDir->createOrGetDirecotry('Precedence');
        $this->precedenceConsumer = $precedenceDir->createFile('PrecedenceTraitConsumer.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesTraitAliasAdaptation(): void
    {
        $this->renameTrait();

        $this->codeMatches($this->aliasConsumer->content(), <<<'PHP'
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

        $this->codeMatches($this->precedenceConsumer->content(), <<<'PHP'
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
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria('LegacyTrait'));
        if ($class->first() === null) {
            $this->fail('Class LegacyTrait not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class->first(), new Identifier('ModernTrait')));
    }
}

