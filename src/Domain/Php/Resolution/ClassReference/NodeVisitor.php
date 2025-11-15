<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Resolution\ClassReference;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\FullyQualifiedName;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpNamespace;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AliasMap;

interface NodeVisitor
{
    /**
     * @return list<ClassReference>
     */
    public function visit(PhpFile $file, PhpNamespace $currentNamespace, Node $node, AliasMap $aliasMap, FullyQualifiedName $targetFullName): array;
}
