<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;

final class FileNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        array $statements
    ) {
        parent::__construct();

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    /**
     * @return list<ClassLikeNode>
     */
    public function classLikeDeclerations(): array
    {
        return $this->children()->getAllOfNestedByType(ClassLikeNode::class);
    }

    /**
     * @return list<UseStatementNode>
     */
    public function useStatements(): array
    {
        return $this->children()->getAllOfNestedByType(UseStatementNode::class);
    }
}
