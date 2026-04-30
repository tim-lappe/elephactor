<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\ClassIndex\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class AnonymousClassRenameTest extends ElephactorTestCase
{
    private VirtualFile $anonymousExtendsUsage;
    private VirtualFile $anonymousImplementsUsage;

    public function setUp(): void
    {
        parent::setUp();

        $this->sourceDirectory
            ->createOrGetDirecotry('Anonymous')
            ->createFile('OldAnonymousBase.php', <<<'PHP'
            <?php

            namespace VirtualTestNamespace\Anonymous;

            class OldAnonymousBase
            {
                public function value(): string
                {
                    return 'base';
                }
            }
            PHP);

        $this->sourceDirectory
            ->createOrGetDirecotry('Anonymous')
            ->createFile('OldAnonymousInterface.php', <<<'PHP'
            <?php

            namespace VirtualTestNamespace\Anonymous;

            interface OldAnonymousInterface
            {
                public function run(): void;
            }
            PHP);

        $this->anonymousExtendsUsage = $this->sourceDirectory
            ->createOrGetDirecotry('AnonymousUsage')
            ->createFile('AnonymousFactory.php', <<<'PHP'
            <?php

            namespace VirtualTestNamespace\AnonymousUsage;

            use VirtualTestNamespace\Anonymous\OldAnonymousBase;

            class AnonymousFactory
            {
                public function create(): object
                {
                    return new class extends OldAnonymousBase
                    {
                        public function marker(): string
                        {
                            return 'extended';
                        }
                    };
                }
            }
            PHP);

        $this->anonymousImplementsUsage = $this->sourceDirectory
            ->createOrGetDirecotry('AnonymousUsage')
            ->createOrGetDirecotry('Interfaces')
            ->createFile('InterfaceAnonymousFactory.php', <<<'PHP'
            <?php

            namespace VirtualTestNamespace\AnonymousUsage\Interfaces;

            class InterfaceAnonymousFactory
            {
                public function create(): object
                {
                    return new class implements \VirtualTestNamespace\Anonymous\OldAnonymousInterface
                    {
                        public function run(): void
                        {
                        }
                    };
                }
            }
            PHP);

        $this->workspace->reloadIndices();
    }

    public function testRenamesAnonymousExtendsReference(): void
    {
        $this->renameTarget('OldAnonymousBase', 'NewAnonymousBase');

        $this->codeMatches($this->anonymousExtendsUsage->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\AnonymousUsage;

        use VirtualTestNamespace\Anonymous\NewAnonymousBase;

        class AnonymousFactory
        {
            public function create(): object
            {
                return new class extends NewAnonymousBase
                {
                    public function marker(): string
                    {
                        return 'extended';
                    }
                };
            }
        }
        PHP);
    }

    public function testRenamesAnonymousImplementsReference(): void
    {
        $this->renameTarget('OldAnonymousInterface', 'NewAnonymousInterface');

        $this->codeMatches($this->anonymousImplementsUsage->content(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\AnonymousUsage\Interfaces;

        class InterfaceAnonymousFactory
        {
            public function create(): object
            {
                return new class implements \VirtualTestNamespace\Anonymous\NewAnonymousInterface
                {
                    public function run(): void
                    {
                    }
                };
            }
        }
        PHP);
    }

    private function renameTarget(string $className, string $newName): void
    {
        $class = $this->workspace->classLikeIndex()->find(new ClassNameCriteria(new Identifier($className)))->first();
        if ($class === null) {
            self::fail(sprintf('Class %s not found in workspace', $className));
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier($newName)));
    }
}
