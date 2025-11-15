<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Model\FileHandle;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;

final class AttributeUsageRenameTest extends ElephactorTestCase
{
    private FileHandle $attribute;
    private FileHandle $simpleUsage;
    private FileHandle $qualifiedUsage;

    protected function setUp(): void
    {
        $this->attribute = $this->setupFile(['Attributes'], 'OldAttribute', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Attributes;

        #[\Attribute(\Attribute::TARGET_ALL)]
        class OldAttribute
        {
            public function __construct(public string $value = '')
            {
            }
        }
        PHP);

        $this->simpleUsage = $this->setupFile(['Usage'], 'SimpleAttributeUsage', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Attributes\OldAttribute;

        #[OldAttribute('simple')]
        class SimpleAttributeUsage
        {
            #[OldAttribute('property')]
            public function demo(): void
            {
            }
        }
        PHP);

        $this->qualifiedUsage = $this->setupFile(['Usage', 'Qualified'], 'QualifiedAttributeUsage', <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        #[\VirtualTestNamespace\Attributes\OldAttribute('qualified')]
        class QualifiedAttributeUsage
        {
            #[\VirtualTestNamespace\Attributes\OldAttribute('method')]
            public function demo(): void
            {
            }
        }
        PHP);
    }

    public function testRenamesImportedAttributeUsage(): void
    {
        $this->renameAttribute();

        $this->codeMatches($this->simpleUsage->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage;

        use VirtualTestNamespace\Attributes\NewAttribute;

        #[NewAttribute('simple')]
        class SimpleAttributeUsage
        {
            #[NewAttribute('property')]
            public function demo(): void
            {
            }
        }
        PHP);
    }

    public function testRenamesFullyQualifiedAttributeUsage(): void
    {
        $this->renameAttribute();

        $this->codeMatches($this->qualifiedUsage->readContent(), <<<'PHP'
        <?php

        namespace VirtualTestNamespace\Usage\Qualified;

        #[\VirtualTestNamespace\Attributes\NewAttribute('qualified')]
        class QualifiedAttributeUsage
        {
            #[\VirtualTestNamespace\Attributes\NewAttribute('method')]
            public function demo(): void
            {
            }
        }
        PHP);
    }

    private function renameAttribute(): void
    {
        $application = $this->buildApplication();
        $class = $application->getClassFinder()->find('OldAttribute');
        if ($class === null) {
            throw new \RuntimeException('Class not found');
        }

        $executor = $application->getRefactoringExecutor();
        $executor->handle(new ClassRename($class, new Identifier('NewAttribute')));
    }
}

