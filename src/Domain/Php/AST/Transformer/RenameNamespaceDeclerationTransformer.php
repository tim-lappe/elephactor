<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\VisitorContext;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\NamespaceDefinitionNode;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\QualifiedNameChanger;

final class RenameNamespaceDeclerationTransformer extends AbsractNodeTransformer
{
    public function __construct(
        private readonly QualifiedName $oldName,
        private readonly QualifiedName $newName,
    ) {
        parent::__construct();
    }

    public function enter(Node $node, VisitorContext $context): void
    {
        if ($node instanceof NamespaceDefinitionNode) {
            if ($node->name()->qualifiedName()->equals($this->oldName)) {
                $newQualifiedName = new QualifiedName($this->newName->parts());
                $this->refactorings->add(new QualifiedNameChanger($node->name(), $newQualifiedName));
            }
        }
    }

    public function leave(Node $node, VisitorContext $context): void
    {
    }
}
