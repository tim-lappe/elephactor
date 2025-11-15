<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class AnonymousClassRenameTest extends ElephactorTestCase
{
    private FileHandle $baseClass;
    private FileHandle $anonymousExtendsUsage;
    private FileHandle $interface;
    private FileHandle $anonymousImplementsUsage;

    protected function setUp(): void
    {
        $this->baseClass = $this->setupFile(['Anonymous'], 'OldAnonymousBase', <<<'PHP'
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

        $this->interface = $this->setupFile(['Anonymous'], 'OldAnonymousInterface', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Anonymous;

        interface OldAnonymousInterface
        {
            public function run(): void;
        }
        PHP);

        $this->anonymousExtendsUsage = $this->setupFile(['AnonymousUsage'], 'AnonymousFactory', <<<'PHP'
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

        $this->anonymousImplementsUsage = $this->setupFile(['AnonymousUsage', 'Interfaces'], 'InterfaceAnonymousFactory', <<<'PHP'
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
    }

    public function testRenamesAnonymousExtendsReference(): void
    {
        $this->renameTarget('OldAnonymousBase', 'NewAnonymousBase');

        $this->codeMatches($this->anonymousExtendsUsage->readContent(), <<<'PHP'
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

        $this->codeMatches($this->anonymousImplementsUsage->readContent(), <<<'PHP'
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
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find($className);
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier($newName)));
    }
}

