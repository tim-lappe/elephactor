<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Analyser;

use TimLappe\Elephactor\Domain\Php\AST\Model\FileNode;
use TimLappe\Elephactor\Domain\Php\Analysis\Model\SemanticFileNode;

final class FileAnalyser
{
    public function __construct(
        private readonly DeclerationAnalyser $declerationAnalyser,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(new DeclerationAnalyser(
            new ClassDeclerationAnalyser(),
            new InterfaceDeclerationAnalyser(),
            new TraitDeclerationAnalyser(),
            new EnumDeclerationAnalyser(),
            new BodyUsageAnalyser(),
        ));
    }

    public function analyse(FileNode $fileNode): SemanticFileNode
    {
        $semanticFileNode = new SemanticFileNode($fileNode);
        $this->declerationAnalyser->analyse($semanticFileNode);

        return $semanticFileNode;
    }
}
