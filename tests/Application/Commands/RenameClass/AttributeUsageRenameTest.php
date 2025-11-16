<?php

declare(strict_types=1);

namespace TimLappe\ElephactorTests\Application\Commands\RenameClass;

use TimLappe\Elephactor\Domain\Php\Index\Criteria\ClassNameCriteria;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Refactoring\Commands\ClassRename;
use TimLappe\ElephactorTests\Application\ElephactorTestCase;
use TimLappe\ElephactorTests\Application\VirtualFile;

final class AttributeUsageRenameTest extends ElephactorTestCase
{
    private VirtualFile $attribute;
    private VirtualFile $simpleUsage;
    private VirtualFile $qualifiedUsage;

    public function setUp(): void
    {
        parent::setUp();

        $attributesDir = $this->sourceDirectory->createOrGetDirecotry('Attributes');
        $this->attribute = $attributesDir->createFile('OldAttribute.php', <<<'PHP'
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

        $usageDir = $this->sourceDirectory->createOrGetDirecotry('Usage');
        $this->simpleUsage = $usageDir->createFile('SimpleAttributeUsage.php', <<<'PHP'
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

        $qualifiedDir = $usageDir->createOrGetDirecotry('Qualified');
        $this->qualifiedUsage = $qualifiedDir->createFile('QualifiedAttributeUsage.php', <<<'PHP'
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

        $this->workspace->reloadIndices();
    }

    public function testRenamesImportedAttributeUsage(): void
    {
        $this->renameAttribute();

        $this->codeMatches($this->simpleUsage->content(), <<<'PHP'
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

        $this->codeMatches($this->qualifiedUsage->content(), <<<'PHP'
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
        $class = $this->workspace->classIndex()->find(new ClassNameCriteria('OldAttribute'));
        if ($class->first() === null) {
            $this->fail('Class OldAttribute not found in workspace');
        }

        $executor = $this->application->refactoringExecutor();
        $executor->handle(new ClassRename($class->first(), new Identifier('NewAttribute')));
    }
}