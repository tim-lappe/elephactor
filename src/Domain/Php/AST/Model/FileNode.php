<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;
use PhpParser\Token;

final class FileNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     * @param array<Token> $originalAdapterTokens
     */
    public function __construct(
        array $statements,
        private readonly array $originalAdapterTokens
    ) {
        parent::__construct();

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    /**
     * @return array<Token>
     */
    public function originalAdapterTokens(): array
    {
        return $this->originalAdapterTokens;
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
