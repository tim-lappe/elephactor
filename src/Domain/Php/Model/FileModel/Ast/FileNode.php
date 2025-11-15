<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement\UseStatementNodeCollection;

final class FileNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly array $statements
    ) {
        parent::__construct(NodeKind::FILE);
    }

    public function useStatements(): UseStatementNodeCollection
    {
        return new UseStatementNodeCollection($this->findNestedChildrenOfType(UseStatementNode::class));
    }

    /**
     * @return ?ClassLikeNode
     */
    public function findClassLikeDeclaration(string $name): ClassLikeNode|null
    {
        $declarations = $this->findNestedChildrenOfType(ClassLikeNode::class);
        foreach ($declarations as $declaration) {
            if ($declaration->name()->equals($name)) {
                return $declaration;
            }
        }

        return null;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->statements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->statements;
    }
}
