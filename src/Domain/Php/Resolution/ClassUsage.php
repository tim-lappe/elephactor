<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Resolution;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\PhpFile;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseStatementNode;

final class ClassUsage
{
    public function __construct(
        private readonly PhpFile $file,
        private readonly UseStatementNode $useStatementNode,
    ) {
    }

    public function file(): PhpFile
    {
        return $this->file;
    }

    public function useStatementNode(): UseStatementNode
    {
        return $this->useStatementNode;
    }
}
